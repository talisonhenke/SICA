<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function gerarPixPayload($chave, $valor, $nomeRecebedor, $cidade, $identificador) 
    {
        $payload = [
            "00" => "01",
            "26" => [
                "00" => "BR.GOV.BCB.PIX",
                "01" => $chave
            ],
            "52" => "0000",
            "53" => "986",
            "54" => number_format($valor, 2, '.', ''),
            "58" => "BR",
            "59" => $nomeRecebedor,
            "60" => strtoupper($cidade),
            "62" => [
                "05" => $identificador
            ]
        ];

        $montar = function ($arr) use (&$montar) {
            $out = "";
            foreach ($arr as $id => $valor) {
                if (is_array($valor)) {
                    $valString = $montar($valor);
                } else {
                    $valString = $valor;
                }
                $out .= $id . str_pad(strlen($valString), 2, '0', STR_PAD_LEFT) . $valString;
            }
            return $out;
        };

        $semCRC = $montar($payload) . "6304";

        $crc = strtoupper(dechex(crc16($semCRC)));

        return $semCRC . $crc;
    }

    function crc16($string) {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($string); $i++) {
            $crc ^= ord($string[$i]) << 8;
            for ($b = 0; $b < 8; $b++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = ($crc << 1);
                }
                $crc &= 0xFFFF;
            }
        }
        return $crc;
    }

    public function gerarPixQR(Request $request)
    {
        $valor = $request->valor;
        $chave = 'sua-chave-aqui';
        $nome  = 'Seu Nome';
        $cidade = 'Sua Cidade';
        $id = 'PED' . rand(1000, 9999);

        $payload = gerarPixPayload($chave, $valor, $nome, $cidade, $id);

        $qr = QrCode::size(300)->generate($payload);

        return view('checkout.pix', compact('qr', 'payload'));
    }

}
