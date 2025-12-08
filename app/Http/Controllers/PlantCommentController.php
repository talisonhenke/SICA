<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\PlantComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PlantCommentController extends Controller
{
    public function store(Request $request, Plant $plant)
    {
        $request->validate([
            'comment' => 'required|min:2|max:2000',
        ]);

        $user = auth()->user();

        if ($user->comment_strikes >= 3) {
            return back()->with('error', 'A função de comentários está temporariamente indisponível para você.');
        }

        try {
            DB::beginTransaction();

            $comment = $request->comment;

            $toxicityScore = (float) $this->checkToxicity($comment);

            if ($toxicityScore === null) {
                throw new \Exception('Perspective API retornou resposta inválida.');
            }

            if ($toxicityScore >= 0.8) {
                DB::rollBack();
                $user->increment('comment_strikes');
                return back()->with('msg', 'Seu comentário foi identificado como inadequado e não pôde ser enviado.');
            }

            $saved = $plant->comments()->create([
                'user_id'        => $user->id,
                'comment'        => $comment,
                'toxicity_level' => $toxicityScore,
                'moderated'      => 0,
                'reported'       => 0,
            ]);

            if (!$saved) {
                throw new \Exception('Falha ao salvar o comentário.');
            }

            DB::commit();
            return back()->with('msg', 'Comentário enviado!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao registrar comentário: ' . $e->getMessage());
            return back()->with('error', 'Erro ao salvar comentário.');
        }
    }

    private function containsWord($text, $word)
    {
        $escaped = preg_quote($word, '/');
        return preg_match('/\b' . $escaped . '\b/u', $text);
    }

    public function update(Request $request, PlantComment $comment)
    {
        $request->validate([
            'comment' => 'required|min:2|max:2000',
        ]);

        $user = auth()->user();

        if ($comment->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para editar este comentário.');
        }

        if ($user->comment_strikes >= 3) {
            return back()->with('error', 'A função de comentários está temporariamente indisponível para você.');
        }

        try {
            DB::beginTransaction();

            $newCommentText = $request->comment;

            $toxicityScore = (float) $this->checkToxicity($newCommentText);

            if ($toxicityScore === null) {
                throw new \Exception('Perspective API retornou resposta inválida.');
            }

            if ($toxicityScore >= 0.8) {
                DB::rollBack();
                $user->increment('comment_strikes');
                return back()->with('error', 'Seu comentário editado foi identificado como inadequado e não pôde ser atualizado.');
            }

            $comment->update([
                'comment'        => $newCommentText,
                'toxicity_level' => $toxicityScore,
                'moderated'      => 0,
                'reported'       => 0,
            ]);

            DB::commit();
            return back()->with('msg', 'Comentário atualizado!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar comentário: ' . $e->getMessage());
            return back()->with('error', 'Erro ao atualizar comentário.');
        }
    }

    public function report($commentId)
    {
        $comment = PlantComment::findOrFail($commentId);

        if (!auth()->check()) {
            return back()->with('error', 'Você precisa estar logado para denunciar.');
        }

        if ($comment->moderated == 1) {
            return back()->with('msg', 'Este comentário já foi moderado.');
        }

        $comment->reported = 1;
        $comment->save();

        return back()->with('msg', 'Comentário denunciado para moderação.');
    }

    public function moderatorApprove($commentId)
    {
        $comment = PlantComment::findOrFail($commentId);

        if (!auth()->user() || auth()->user()->user_lvl !== 'admin') {
            return back()->with('error', 'Apenas administradores podem moderar.');
        }

        $comment->update([
            'moderated' => 1,
            'reported'  => 0,
        ]);

        return back()->with('success', 'Comentário marcado como analisado.');
    }

    public function moderateDelete($commentId)
    {
        $comment = PlantComment::findOrFail($commentId);

        if (!auth()->user() || auth()->user()->user_lvl !== 'admin') {
            return back()->with('error', 'Apenas administradores podem moderar.');
        }

        $user = $comment->user;

        $user->comment_strikes++;
        $user->save();

        $comment->delete();

        return back()->with('success', 'Comentário excluído e strike aplicado.');
    }

    private function checkToxicity($text)
    {
        $apiKey = env('PERSPECTIVE_API_KEY');

        $badwordsPath = storage_path('app/private/badwords.json');
        $badwords = json_decode(file_get_contents($badwordsPath), true);

        $unacceptable = $badwords['unacceptable'] ?? [];
        $suspect      = $badwords['suspect'] ?? [];

        $textLower = mb_strtolower($text);

        foreach ($unacceptable as $word) {
            if ($this->containsWord($textLower, $word)) {
                return 0.9;
            }
        }

        try {
            $response = Http::timeout(10)->post(
                "https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key={$apiKey}",
                [
                    'comment' => ['text' => $text],
                    'languages' => ['pt', 'en'],
                    'requestedAttributes' => ['TOXICITY' => new \stdClass()],
                ]
            );

            $json  = $response->json();
            $score = $json['attributeScores']['TOXICITY']['summaryScore']['value'] ?? 0.0;
        } catch (\Throwable $e) {
            Log::error('Erro API Perspective: ' . $e->getMessage());
            $score = 0.0;
        }

        $extra = 0.0;

        foreach ($suspect as $word) {
            if ($this->containsWord($textLower, $word)) {
                $extra += 0.4;

                if ($extra >= 0.7) {
                    return $extra;
                }
            }
        }

        return min($score + $extra, 1.0);
    }

    public function destroy($id)
    {
        $comment = PlantComment::findOrFail($id);

        if (auth()->id() !== $comment->user_id) {
            return redirect()->back()->with('error', 'Você não tem permissão para excluir este comentário.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comentário excluído com sucesso.');
    }

    public function allow($id)
    {
        $comment = PlantComment::findOrFail($id);

        $comment->moderated = 1;
        $comment->reported = 0;
        $comment->save();

        return back()->with('success', 'Comentário permitido e removido da moderação.');
    }

    public function blockUser($userId)
    {
        $user = User::findOrFail($userId);

        $user->comment_strikes = 3;
        $user->save();

        return back()->with('success', 'Usuário bloqueado para fazer comentários.');
    }
}
