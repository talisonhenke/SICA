<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // chapix de quem vai receber 
        $chave = "fdcf6a38-1173-4f7f-bc9b-328c37297fbf";

        // 1️⃣ Pegamos o carrinho da sessão
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        // 2️⃣ Calculamos o total
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // 3️⃣ Criamos o pedido
        $order = Order::create([
            'user_id'       => Auth::user()->id,
            'total_amount'  => $total,
            'order_address' => null,      // vamos implementar depois
            'status'        => 'pending',
            'order_pix'     => 'generating'         // vamos preencher depois
        ]);

        // 4️⃣ Criamos os itens do pedido
        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $productId,
                'quantity'   => $item['quantity'],
                'price'      => $item['price']
            ]);
        }

        // 5️⃣ Registramos histórico
        OrderHistory::create([
            'order_id' => $order->id,
            'status'   => 'pending',
            'notes'    => 'Pedido criado e aguardando pagamento.',
        ]);

        // 6️⃣ Criamos o PIX (método idêntico ao que você testou ontem)
        $payload = $this->gerarPayloadPix($chave,$total);

        // salvamos o código no pedido
        $order->update([
            'order_pix' => $payload
        ]);

        // 7️⃣ Limpamos o carrinho
        session()->forget('cart');

        // 8️⃣ Redirecionamos o usuário para a página de pagamento
        return redirect()->route('orders.payment', $order->id);
    }

    /*
     * Gerar QR Code PIX (mesmo algoritmo de ontem)
     */
private function crc16($payload)
{
    $polynomial = 0x1021;
    $result = 0xFFFF;

    for ($i = 0; $i < strlen($payload); $i++) {
        $result ^= (ord($payload[$i]) << 8);
        for ($j = 0; $j < 8; $j++) {
            if ($result & 0x8000) {
                $result = ($result << 1) ^ $polynomial;
            } else {
                $result = $result << 1;
            }
            $result &= 0xFFFF;
        }
    }

    return strtoupper(str_pad(dechex($result), 4, '0', STR_PAD_LEFT));
}


    public function montarPix(array $data)
    {
        $result = '';

        foreach ($data as $id => $value) {
            if (is_array($value)) {
                $raw = $this->montarPix($value);
            } else {
                $raw = $value;
            }

            $len = strlen($raw);
            $result .= $id . str_pad($len, 2, '0', STR_PAD_LEFT) . $raw;
        }

        return $result . "6304" . $this->crc16($result);
    }

    private function gerarPayloadPix($chavePix, $valor)
{
    $merchantAccountInfo =
        $this->emvField("00", "BR.GOV.BCB.PIX") .
        $this->emvField("01", $chavePix);

    $merchantAccountInfo = $this->emvField("26", $merchantAccountInfo);

    $payload =
        $this->emvField("00", "01") .                // Payload format
        $merchantAccountInfo .
        $this->emvField("52", "0000") .             // Merchant category
        $this->emvField("53", "986") .              // Currency
        $this->emvField("54", number_format($valor, 2, '.', '')) .
        $this->emvField("58", "BR") .
        $this->emvField("59", "SICA Online") .
        $this->emvField("60", "PELOTAS") .
        $this->emvField("62", $this->emvField("05", "SICA1234"));

    // Adiciona campo do CRC (sem o CRC ainda)
    $payload .= "6304";

    // Agora calcula o CRC16
    $crc = $this->crc16($payload);

    return $payload . $crc;
}

    private function emvField($id, $value)
{
    $len = strlen($value);
    return $id . str_pad($len, 2, '0', STR_PAD_LEFT) . $value;
}
    
    public function paymentPage(Order $order)
    {
        $qrcodeSvg = \QrCode::size(280)->generate($order->order_pix);

        return view('orders.payment', [
            'order' => $order,
            'qrcodeSvg' => $qrcodeSvg,
            'payload' => $order->order_pix
        ]);
    }
}
