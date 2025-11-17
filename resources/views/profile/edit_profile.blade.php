@extends('layouts.main')

@include('includes.toast')

@section('content')

<style>
    /* --- ESTILIZAÇÃO USANDO AS CORES GLOBAIS --- */
    :root {
        --primary-color: #4CAF50;     /* Verde padrão */
        --secondary-color: #2E7D32;   /* Verde escuro */
        --accent-color: #FFC107;      /* Amarelo */
        --danger-color: #dc3545;      /* Vermelho padrão */
        --light-color: #f8f9fa;
        --dark-color: #343a40;
    }

    .profile-card {
        max-width: 500px;
        margin: auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .profile-header {
        background: var(--primary-color);
        color: white;
    }

    .modal-header {
        background: var(--primary-color);
        color: white;
    }

    .address-box {
        padding: 1rem;
        border-radius: 10px;
        background: var(--light-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .address-entry {
        border-left: 4px solid var(--primary-color);
        padding-left: 10px;
        margin-bottom: 10px;
    }

    .add-address-btn {
        background: var(--primary-color);
        border: none;
        color: white;
        font-weight: 600;
    }

    .add-address-btn:hover {
        background: var(--secondary-color);
        color: white;
    }

</style>

<div class="container my-4">

    <h1 class="text-center mb-4">Editar Perfil</h1>

    <!-- CARD DO PERFIL -->
    <div class="card profile-card shadow-sm">

        <div class="card-header profile-header">
            <h5 class="mb-0">Informações do Usuário</h5>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Nome:</strong> {{ $user->name }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                <li class="list-group-item"><strong>Nível de Usuário:</strong> {{ $levels[$user->user_lvl] ?? $user->user_lvl }}</li>
            </ul>

            <div class="d-grid">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editNameModal">
                    Editar
                </button>
            </div>
        </div>
    </div>

    <!-- SEÇÃO DE ENDEREÇOS -->
    <div class="mt-5">
        <h3 class="text-center mb-3">Meus Endereços</h3>

        <div class="address-box">

            <!-- Lista de endereços -->
            @forelse($addresses as $address)
                <div class="address-entry">
                    <strong>{{ $address->street }}, {{ $address->number }}</strong><br>
                    {{ $address->city }} - {{ $address->state }}<br>
                    CEP: {{ $address->zip_code }}
                    <div class="mt-2">
                        <a href="#" class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="#" class="btn btn-sm btn-outline-danger">Excluir</a>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center">Nenhum endereço cadastrado.</p>
            @endforelse

            <!-- MODAL GOOGLE ADDRESS -->
            {{-- @include('includes.google_address_modal') --}}
            <div class="modal fade" id="googleAddressModal" tabindex="-1">
    <div class="modal-dialog modal-xl">

        <form action="{{ route('addresses.store') }}" method="POST">
            @csrf

            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">

                <!-- HEADER -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Selecionar Endereço</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- AUTOCOMPLETE -->
                    <label class="fw-bold mb-1">Pesquisar endereço</label>
                    <input id="googleAutocomplete" type="text" class="form-control form-control-lg mb-3"
                           placeholder="Digite um endereço, rua, bairro, estabelecimento..." />

                    <!-- MAPA -->
                    <div id="googleMap" style="height: 420px; border-radius: 10px;"></div>

                    <hr class="my-4">

                    <!-- FORMULÁRIO DE CAMPOS -->
                    <h5 class="fw-bold mb-3">Dados do Endereço</h5>

                    <div class="row">

                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Rua</label>
                            <input type="text" id="street" name="street" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Número</label>
                            <input type="text" id="number" name="number" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Complemento</label>
                            <input type="text" id="complement" name="complement" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Bairro</label>
                            <input type="text" id="district" name="district" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Cidade</label>
                            <input type="text" id="city" name="city" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Estado</label>
                            <input type="text" id="state" name="state" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">CEP</label>
                            <input type="text" id="zip_code" name="zip_code" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">País</label>
                            <input type="text" id="country" name="country" class="form-control" required>
                        </div>

                    </div>

                    <!-- HIDDEN FIELDS -->
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-success">
                        Salvar Endereço
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>

            <div class="d-grid mt-3">
                <button class="btn add-address-btn" data-bs-toggle="modal" data-bs-target="#googleAddressModal">
                    + Adicionar Endereço
                </button>
            </div>

        </div>
    </div>

</div>





<!-- --------------------------- -->
<!-- MODAL: EDITAR NOME -->
<!-- --------------------------- -->
<div class="modal fade" id="editNameModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('profile.updateName') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Nome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-secondary"
                        data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                        data-bs-dismiss="modal">
                        Alterar Senha
                    </button>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Nome</button>
            </div>

        </div>
    </form>
  </div>
</div>





<!-- --------------------------- -->
<!-- MODAL: ALTERAR SENHA -->
<!-- --------------------------- -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('profile.updatePassword') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Alterar Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Senha Atual</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nova Senha</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmar Nova Senha</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>

        </div>
    </form>
  </div>
</div>

{{-- ============================= --}}
{{-- GOOGLE MAPS + PLACES SCRIPT   --}}
{{-- ============================= --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places"></script>

<script>

let map;
let marker;
let geocoder;
let autocomplete;

document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("googleAddressModal");

        modal.addEventListener("shown.bs.modal", () => {

        setTimeout(() => {

            initGoogleMap();

            // 🔥 Agora o input está visível → inicializa o autocomplete aqui
            initAutocomplete();

        }, 600);

    });


});

/* ===========================================================
   INICIALIZAÇÃO DO MAPA (AGORA COM GEOLOCALIZAÇÃO)
=========================================================== */
function initGoogleMap() {

    if (map) {
        google.maps.event.trigger(map, "resize");
        return;
    }

    geocoder = new google.maps.Geocoder();

    const fallbackLatLng = { lat: -23.5505, lng: -46.6333 }; // fallback SP

    // 1️⃣ Primeiro tenta pegar localização real do usuário
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const userLatLng = {
                lat: pos.coords.latitude,
                lng: pos.coords.longitude
            };
            initializeMap(userLatLng);
        },
        () => {
            // Se o usuário negar → usar fallback
            initializeMap(fallbackLatLng);
        }
    );
}

/* ===========================================================
   FUNÇÃO QUE REALMENTE MONTA O MAPA
=========================================================== */
function initializeMap(centerLatLng) {

    map = new google.maps.Map(document.getElementById("googleMap"), {
        center: centerLatLng,
        zoom: 15,
        streetViewControl: false,
        mapTypeControl: false,
        fullscreenControl: false
    });

    marker = new google.maps.Marker({
        position: centerLatLng,
        map: map,
        draggable: true
    });

    marker.addListener("dragend", () => {
        const pos = marker.getPosition();
        updateAddressFromLatLng(pos.lat(), pos.lng());
    });

    map.addListener("click", (e) => {
        marker.setPosition(e.latLng);
        updateAddressFromLatLng(e.latLng.lat(), e.latLng.lng());
    });
}

/* ===========================================================
   AUTOCOMPLETE DO GOOGLE PLACES
=========================================================== */
function initAutocomplete() {

    const input = document.getElementById("googleAutocomplete");

    autocomplete = new google.maps.places.Autocomplete(input, {
        fields: ["address_components", "geometry"],
        componentRestrictions: { country: "br" }
    });

    autocomplete.addListener("place_changed", () => {

        const place = autocomplete.getPlace();
        if (!place.geometry) return;

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        map.setCenter({ lat, lng });
        map.setZoom(18);

        marker.setPosition({ lat, lng });

        fillAddressFields(place.address_components);

        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
    });
}

/* ===========================================================
   REVERSE GEOCODING
=========================================================== */
function updateAddressFromLatLng(lat, lng) {

    geocoder.geocode({ location: { lat, lng } }, (results, status) => {

        if (status === "OK" && results[0]) {

            fillAddressFields(results[0].address_components);

            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
        }
    });
}

/* ===========================================================
   EXTRAÇÃO DOS CAMPOS
=========================================================== */
function getPart(components, type) {
    let obj = components.find(c => c.types.includes(type));
    return obj ? obj.long_name : "";
}

function fillAddressFields(components) {

    document.getElementById("street").value =
        getPart(components, "route");

    document.getElementById("number").value =
        getPart(components, "street_number");

    document.getElementById("city").value =
        getPart(components, "administrative_area_level_2");

    document.getElementById("state").value =
        getPart(components, "administrative_area_level_1");

    document.getElementById("zip_code").value =
        getPart(components, "postal_code");

    document.getElementById("country").value =
        getPart(components, "country");
}

</script>


@endsection