@extends('layouts.main')

@section('content')

<div class="container bg-white text-center mt-4">

    <h2>Pagamento via PIX</h2>
    <p>Valor da compra: <strong>R$ {{ number_format($valor, 2, ',', '.') }}</strong></p>

    <div class="mt-4">
        {!! $qrcodeSvg !!}
    </div>

    <p class="mt-3 text-muted">
        Escaneie o QR Code para realizar o pagamento
    </p>

    <textarea class="form-control mt-3" rows="4" readonly>{{ $payload }}</textarea>
    <small class="text-muted">Caso seu banco precise do código copia e cola</small>

</div>

@endsection
