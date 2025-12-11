<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Exibe a lista de produtos.
     */
    public function index()
    {
        $products = Product::with('plant')->get();
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with('plant')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create()
    {
        $plants = Plant::all();
        return view('products.create', compact('plants'));
    }

    /**
     * Armazena um novo produto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'plant_id' => 'required|exists:plants,id',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'status' => 'required|boolean',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5021',
            ],
            [
                'plant_id.required' => 'Selecione a planta relacionada ao produto.',
                'plant_id.exists' => 'A planta selecionada não é válida.',

                'name.required' => 'O nome do produto é obrigatório.',
                'name.max' => 'O nome do produto não pode ultrapassar 255 caracteres.',

                'description.required' => 'Informe a descrição do produto',

                'price.required' => 'Informe o preço do produto.',
                'price.numeric' => 'O preço deve ser um número válido.',
                'price.min' => 'O preço não pode ser negativo.',

                'stock.required' => 'Informe a quantidade em estoque.',
                'stock.integer' => 'O estoque deve ser um número inteiro.',
                'stock.min' => 'O estoque não pode ser negativo.',

                'status.required' => 'Selecione o status do produto.',
                'status.boolean' => 'O status informado é inválido.',

                'image.required' => 'Envie uma imagem do produto.',
                'image.image' => 'O arquivo enviado deve ser uma imagem.',
                'image.mimes' => 'A imagem deve ser JPEG, PNG, JPG ou GIF.',
                'image.max' => 'A imagem não pode ter mais que 2MB.',
            ],
        );

        $product = Product::create($validated);

        // Salvar imagem na pasta "images/products/{id}/"
        if ($request->hasFile('image')) {
            $folderPath = public_path('images/products/' . $product->id);

            // Cria a pasta se não existir
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move($folderPath, $filename);

            $product->image = 'images/products/' . $product->id . '/' . $filename;
            $product->save();
        }

        return redirect()->route('products.index')->with('msg', 'Produto criado com sucesso!');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Product $product)
    {
        $plants = Plant::all();
        return view('products.edit', compact('product', 'plants'));
    }

    /**
     * Atualiza o produto existente.
     */
    public function update(Request $request, Product $product)
    {
        // Validações com mensagens traduzidas
        $validated = $request->validate(
            [
                'plant_id' => ['required', 'exists:plants,id'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'stock' => ['required', 'integer', 'min:0'],
                'status' => ['required', 'boolean'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ],
            [
                // Traduções
                'plant_id.required' => 'Selecione a planta relacionada.',
                'plant_id.exists' => 'A planta selecionada é inválida.',

                'name.required' => 'O nome do produto é obrigatório.',
                'name.string' => 'O nome precisa ser um texto válido.',
                'name.max' => 'O nome pode ter no máximo 255 caracteres.',

                'description.string' => 'A descrição deve ser um texto válido.',

                'price.required' => 'Informe o preço do produto.',
                'price.numeric' => 'O preço deve ser um valor numérico.',
                'price.min' => 'O preço não pode ser negativo.',

                'stock.required' => 'Informe o estoque do produto.',
                'stock.integer' => 'O estoque deve ser um número inteiro.',
                'stock.min' => 'O estoque não pode ser negativo.',

                'status.required' => 'Selecione o status do produto.',
                'status.boolean' => 'O status informado é inválido.',

                'image.image' => 'O arquivo enviado deve ser uma imagem.',
                'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou gif.',
                'image.max' => 'A imagem deve ter no máximo 2MB.',
            ],
        );

        // Atualiza campos básicos
        $product->update($validated);

        // Atualização da imagem
        if ($request->hasFile('image')) {
            $folderPath = public_path('images/products/' . $product->id);

            // Cria pasta se não existir
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            // Remove imagem antiga
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            // Salva nova imagem
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move($folderPath, $filename);

            // Atualiza caminho no banco
            $product->image = 'images/products/' . $product->id . '/' . $filename;
            $product->save();
        }

        return redirect()->route('products.index')->with('msg', 'Produto atualizado com sucesso!');
    }

    /**
     * Exclui o produto.
     */
    public function destroy(Product $product)
    {
        // Apaga imagem e pasta
        if ($product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        $folderPath = public_path('images/products/' . $product->id);
        if (File::exists($folderPath)) {
            File::deleteDirectory($folderPath);
        }

        $product->delete();

        return redirect()->route('products.index')->with('msg', 'Produto excluído com sucesso!');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = !$product->status;
        $product->save();

        return response()->json(['success' => true]);
    }
}
