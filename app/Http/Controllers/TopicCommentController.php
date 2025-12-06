<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\TopicComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TopicCommentController extends Controller
{
    public function store(Request $request, Topic $topic)
    {
        $request->validate([
            'comment' => 'required|min:2|max:2000',
        ]);

        $user = auth()->user();

        // ====== BLOQUEIO POR 3 STRIKES ======
        if ($user->comment_strikes >= 3) {
            return back()->with('error', 'A função de comentários está temporariamente indisponível para você.');
        }

        try {
            DB::beginTransaction();

            $comment = $request->comment;

            $toxicityScore = (float) $this->checkToxicity($comment);
            // dd($toxicityScore);

            if ($toxicityScore === null) {
                throw new \Exception('Perspective API retornou resposta inválida.');
            }

            // ====== 1) REGRA DE BLOQUEIO POR TOXICIDADE ======
            if ($toxicityScore >= 0.8) {
                DB::rollBack();
                $user->increment('comment_strikes');
                return back()->with('msg', 'Seu comentário foi identificado como inadequado e não pôde ser enviado.');
            }

            // ====== 2) SALVA O COMENTÁRIO ======
            $saved = $topic->comments()->create([
                'user_id' => $user->id,
                'comment' => $comment,
                'toxicity_level' => $toxicityScore,
            ]);

            if (!$saved) {
                throw new \Exception('Falha ao salvar o comentário no banco de dados.');
            }

            DB::commit();
            return back()->with('msg', 'Comentário enviado!');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Erro ao registrar comentário: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Erro ao salvar comentário.');
        }
    }

    public function update(Request $request, TopicComment $comment)
    {
        $request->validate([
            'comment' => 'required|min:2|max:2000',
        ]);

        $user = auth()->user();

        // ====== GARANTE QUE SOMENTE O AUTOR PODE EDITAR ======
        if ($comment->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para editar este comentário.');
        }

        // ====== BLOQUEIO POR 3 STRIKES ======
        if ($user->comment_strikes >= 3) {
            return back()->with('error', 'A função de comentários está temporariamente indisponível para você.');
        }

        try {
            DB::beginTransaction();

            $newCommentText = $request->comment;

            // ====== VERIFICA TOXICIDADE ======
            $toxicityScore = (float) $this->checkToxicity($newCommentText);

            if ($toxicityScore === null) {
                throw new \Exception('Perspective API retornou resposta inválida.');
            }

            // ====== BLOQUEIO SE TOXICIDADE >= 0.8 ======
            if ($toxicityScore >= 0.8) {
                DB::rollBack();
                $user->increment('comment_strikes');

                return back()->with('error', 'Seu comentário editado foi identificado como inadequado e não pôde ser atualizado.');
            }

            // ====== ATUALIZA O COMENTÁRIO ======
            $comment->update([
                'comment' => $newCommentText,
                'toxicity_level' => $toxicityScore,
            ]);

            DB::commit();
            return back()->with('msg', 'Comentário atualizado!');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar comentário: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Erro ao atualizar comentário.');
        }
    }

    private function checkToxicity($text)
    {
        $apiKey = env('PERSPECTIVE_API_KEY');

        try {
            $response = Http::timeout(10)->post("https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key={$apiKey}", [
                'comment' => ['text' => $text],
                'languages' => ['pt', 'en'],
                'requestedAttributes' => [
                    'TOXICITY' => new \stdClass(),
                ],
            ]);

            $json = $response->json();

            // Pegando o valor correto
            return $json['attributeScores']['TOXICITY']['summaryScore']['value'] ?? 0.0;
        } catch (\Throwable $e) {
            Log::error('Erro ao chamar Perspective API: ' . $e->getMessage());
            return 0.0; // Falhou? Considera não tóxico (ou pode bloquear)
        }
    }

    public function destroy($id)
    {
        $comment = TopicComment::findOrFail($id);

        // Somente o dono do comentário pode excluir
        if (auth()->id() !== $comment->user_id) {
            return redirect()->back()->with('error', 'Você não tem permissão para excluir este comentário.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comentário excluído com sucesso.');
    }

    public function moderateDelete($commentId)
    {
        $comment = TopicComment::findOrFail($commentId);

        // Garantir que só admin acesse
        if (!auth()->user()->user_lvl == 'admin') {
            return redirect()->back()->with('error', 'Apenas administradores podem moderar comentários.');
        }

        // Usuário autor do comentário
        $user = $comment->user;

        if (!$user) {
            return redirect()->back()->with('error', 'Usuário não encontrado.');
        }

        // Incrementa strike
        $user->comment_strikes = $user->comment_strikes + 1;
        $user->save();

        // Exclui o comentário
        $comment->delete();

        return redirect()->back()->with('success', 'Comentário excluído e strike aplicado.');
    }
}
