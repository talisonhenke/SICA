@extends('layouts.main')

@section('content')
<div class="container text-center mt-5">
    <h2>📷 Leitor de QR Code</h2>
    <p class="text-muted">Aponte a câmera para o QR Code da planta.</p>

    <div id="reader" style="width: 320px; margin: auto;"></div>
    <div id="result" class="mt-3 fw-bold text-success"></div>
</div>

{{-- Biblioteca HTML5 QRCode --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const resultContainer = document.getElementById("result");

    function onScanSuccess(decodedText, decodedResult) {
        resultContainer.innerText = `Código detectado: ${decodedText}`;
        
        // Se for um link, redireciona automaticamente
        if (decodedText.startsWith("http")) {
            window.location.href = decodedText;
        }
    }

    function onScanError(errorMessage) {
        // Erros de leitura são comuns, então não precisamos logar a cada frame
    }

    const html5QrCode = new Html5Qrcode("reader");
    const config = { fps: 10, qrbox: 250 };

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            html5QrCode.start(
                devices[0].id, 
                config, 
                onScanSuccess, 
                onScanError
            );
        } else {
            resultContainer.innerText = "Nenhuma câmera encontrada 😕";
        }
    }).catch(err => {
        resultContainer.innerText = "Erro ao acessar câmera: " + err;
    });
});
</script>
@endsection
