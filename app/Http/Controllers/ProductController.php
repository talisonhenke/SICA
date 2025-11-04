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
        $validated = $request->validate([
            'plant_id' => 'required|exists:plants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
        $validated = $request->validate([
            'plant_id' => 'required|exists:plants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update($validated);

        // Atualizar imagem (substituir e apagar a antiga)
        if ($request->hasFile('image')) {
            $folderPath = public_path('images/products/' . $product->id);

            // Cria pasta se não existir
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            // Apaga imagem antiga se existir
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            // Salva nova imagem
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move($folderPath, $filename);

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