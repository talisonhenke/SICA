<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CheckoutController extends Controller
{
    public function show()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        session()->put('cart_total', $total);

        return view('checkout.pix', ['total' => $total]);
    }

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


    public function pix()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        session()->put('cart_total', $total);
        
        $valor = session('cart_total');
        $chave = "fdcf6a38-1173-4f7f-bc9b-328c37297fbf";

        $payload = $this->gerarPayloadPix($chave, $valor);

        $qrcodeSvg = QrCode::size(280)->generate($payload);

        return view('checkout.pix', [
            'payload' => $payload,
            'qrcodeSvg' => $qrcodeSvg,
            'valor' => $valor
        ]);
    }
}
