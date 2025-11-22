<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    // Recupera o carrinho da sessão, ou um array vazio se não existir
    $cart = session()->get('cart', []);
    // Calcula o total
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Retorna a view do carrinho, com as variáveis
    return view('cart.index', compact('cart', 'total'));
}

public function add($id){
    $product = Product::findOrFail($id);

    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            "name" => $product->name,
            "quantity" => 1,
            "price" => $product->price,
            "image" => $product->image,
        ];
    }

    session()->put('cart', $cart);

    if (request()->ajax()) {
        return response()->json(['message' => 'Produto adicionado ao carrinho!']);
    }

    return redirect()->route('cart.index')->with('success', 'Produto adicionado ao carrinho!');
}

public function remove($id)
{
    // Recupera o carrinho atual da sessão
    $cart = session()->get('cart', []);

    // Se o produto existir no carrinho, remove ele
    if (isset($cart[$id])) {
        unset($cart[$id]);
        session()->put('cart', $cart);
    }

    // Redireciona de volta com mensagem
    return redirect()->route('cart.index')->with('success', 'Produto removido do carrinho com sucesso!');
}

public function clear()
{
    // Remove completamente o carrinho da sessão
    session()->forget('cart');

    // Redireciona de volta para o carrinho com mensagem de sucesso
    return redirect()->route('cart.index')->with('success', 'Carrinho esvaziado com sucesso!');
}

public function updateQuantity(Request $request, $id)
{
    $cart = session()->get('cart', []);

    if (!isset($cart[$id])) {
        return back()->with('error', 'Produto não encontrado no carrinho.');
    }

    $action = $request->input('action');

    if ($action === 'increment') {
        $cart[$id]['quantity'] += 1;
    }

    if ($action === 'decrement') {
        $cart[$id]['quantity'] -= 1;

        // Se chegou em zero, remove o produto
        if ($cart[$id]['quantity'] <= 0) {
            unset($cart[$id]);
        }
    }

    session()->put('cart', $cart);

    return back()->with('msg', 'Quantidade atualizada.');
}

}
