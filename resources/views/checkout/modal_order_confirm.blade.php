<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">Finalizar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">

                {{-- Formas de Pagamento --}}
                <h6 class="fw-bold">Forma de Pagamento</h6>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="radio" name="payment_method" id="paymentPix" value="pix" checked>
                    <label class="form-check-label" for="paymentPix">
                        PIX (única opção disponível no momento)
                    </label>
                </div>


                {{-- Endereços --}}
                <h6 class="fw-bold mt-3">Selecione o Endereço de Entrega</h6>

                @php
                    if (!Auth::check()) {
                        // Redireciona imediatamente se não estiver logado
                        header("Location: " . route('login'));
                        exit;
                    }

                    // Usuário autenticado → carregar endereços
                    $addresses = Auth::user()->addresses;
                    $hasAddresses = $addresses->count() > 0;
                @endphp


                @if ($hasAddresses)

                    <div class="mb-3">
                        <label for="addressSelect" class="form-label fw-bold">Selecione um endereço</label>

                        <select id="addressSelect" name="selected_address" class="form-select">
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}"
                                    {{ $address->is_primary ? 'selected' : '' }}>
                                    {{ $address->street }}, {{ $address->number }} — {{ $address->district }} — {{ $address->city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                @else

                    <div class="alert alert-warning">
                        Você ainda não possui endereços cadastrados.  
                        Use o formulário abaixo para adicionar seu endereço de entrega.
                    </div>

                    {{-- Mesmo assim, já deixamos o select pronto para receber o primeiro endereço --}}
                    <div class="mb-3">
                        <label for="addressSelect" class="form-label fw-bold">Selecione um endereço</label>
                        <select id="addressSelect" name="selected_address" class="form-select">
                            {{-- Será preenchido via AJAX --}}
                        </select>
                    </div>

                @endif


                {{-- Botão para adicionar novo endereço --}}
                <button type="button" class="btn btn-outline-primary w-100 mb-3" data-bs-toggle="collapse" data-bs-target="#newAddressForm">
                    + Adicionar novo endereço
                </button>


                {{-- Formulário de novo endereço --}}
                <div class="collapse" id="newAddressForm">

                    <div class="card card-body">

                        <h6 class="fw-bold">Novo Endereço</h6>

                        <!-- MAPA -->
                        <div id="checkoutMap" style="height: 320px; border-radius: 8px;" class="mb-3"></div>

                        <!-- AUTOCOMPLETE -->
                        <label class="form-label fw-semibold">Pesquisar endereço</label>
                        <input id="checkoutAutocomplete" type="text" class="form-control mb-3"
                            placeholder="Digite um endereço, rua, bairro..." />

                        <!-- CAMPOS -->
                        <div class="mb-2">
                            <label class="form-label">Rua</label>
                            <input type="text" class="form-control" id="new_street">
                        </div>

                        <div class="row">
                            <div class="col-4 mb-2">
                                <label class="form-label">Número</label>
                                <input type="text" class="form-control" id="new_number">
                            </div>
                            <div class="col-8 mb-2">
                                <label class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="new_district">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-8 mb-2">
                                <label class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="new_city">
                            </div>
                            <div class="col-4 mb-2">
                                <label class="form-label">CEP</label>
                                <input type="text" class="form-control" id="new_zipcode">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Observação (opcional)</label>
                            <input type="text" class="form-control" id="new_note">
                        </div>

                        <input type="hidden" id="new_lat">
                        <input type="hidden" id="new_lng">

                        <button type="button" class="btn btn-primary w-100 mt-2" id="btnSaveAddress" onclick="saveNewAddress()">
                            Salvar Endereço
                        </button>

                    </div>

                </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-success">
                    Confirmar e Continuar
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places"></script>
<script>
let checkoutMap;
let checkoutMarker;
let checkoutGeocoder;
let checkoutAutocomplete;

document.addEventListener("DOMContentLoaded", function () {

    const collapse = document.getElementById("newAddressForm");

    collapse.addEventListener("shown.bs.collapse", () => {
        setTimeout(initCheckoutMap, 300);
    });

});

/* MAPA DO CHECKOUT */
function initCheckoutMap() {

    if (checkoutMap) {
        google.maps.event.trigger(checkoutMap, "resize");
        return;
    }

    checkoutGeocoder = new google.maps.Geocoder();

    const defaultPos = { lat: -23.5505, lng: -46.6333 };

    navigator.geolocation.getCurrentPosition(
        (pos) => initCheckoutMapInstance({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
        () => initCheckoutMapInstance(defaultPos)
    );
}

function initCheckoutMapInstance(centerLatLng) {

    checkoutMap = new google.maps.Map(document.getElementById("checkoutMap"), {
        center: centerLatLng,
        zoom: 15,
        streetViewControl: false,
        mapTypeControl: false,
        fullscreenControl: false,
    });

    checkoutMarker = new google.maps.Marker({
        position: centerLatLng,
        map: checkoutMap,
        draggable: true
    });

    checkoutMarker.addListener("dragend", () => {
        const pos = checkoutMarker.getPosition();
        updateCheckoutFields(pos.lat(), pos.lng());
    });

    checkoutMap.addListener("click", (e) => {
        checkoutMarker.setPosition(e.latLng);
        updateCheckoutFields(e.latLng.lat(), e.latLng.lng());
    });

    initCheckoutAutocomplete();
}

/* AUTOCOMPLETE */
function initCheckoutAutocomplete() {

    const input = document.getElementById("checkoutAutocomplete");

    checkoutAutocomplete = new google.maps.places.Autocomplete(input, {
        fields: ["address_components", "geometry"],
        componentRestrictions: { country: "br" }
    });

    checkoutAutocomplete.addListener("place_changed", () => {

        const place = checkoutAutocomplete.getPlace();
        if (!place.geometry) return;

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        checkoutMap.setCenter({ lat, lng });
        checkoutMap.setZoom(18);

        checkoutMarker.setPosition({ lat, lng });

        fillCheckoutFields(place.address_components);

        document.getElementById("new_lat").value = lat;
        document.getElementById("new_lng").value = lng;
    });
}

/* REVERSE GEOCODING */
function updateCheckoutFields(lat, lng) {

    checkoutGeocoder.geocode({ location: { lat, lng } }, (results, status) => {

        if (status === "OK" && results[0]) {

            fillCheckoutFields(results[0].address_components);

            document.getElementById("new_lat").value = lat;
            document.getElementById("new_lng").value = lng;
        }
    });
}

/* PREENCHER CAMPOS */
function getPart(components, type) {
    let obj = components.find(c => c.types.includes(type));
    return obj ? obj.long_name : "";
}

function fillCheckoutFields(comp) {

    document.getElementById("new_street").value   = getPart(comp, "route");
    document.getElementById("new_number").value   = getPart(comp, "street_number");

    const districtValue =
        getPart(comp, "sublocality_level_1") ||
        getPart(comp, "sublocality") ||
        "";

    document.getElementById("new_district").value = districtValue;

    document.getElementById("new_city").value     = getPart(comp, "administrative_area_level_2");
    document.getElementById("new_zipcode").value  = getPart(comp, "postal_code");

}
</script>

<script>
function saveNewAddress() {
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formData = new FormData();
    formData.append('street', document.getElementById('new_street').value);
    formData.append('number', document.getElementById('new_number').value);
    formData.append('district', document.getElementById('new_district').value);
    formData.append('city', document.getElementById('new_city').value);
    formData.append('zipcode', document.getElementById('new_zipcode').value);
    formData.append('note', document.getElementById('new_note').value);
    formData.append('latitude', document.getElementById('new_lat').value);
    formData.append('longitude', document.getElementById('new_lng').value);


    //TODO: Verificar e validar resposta do método storeByCheckout

    fetch("{{ route('addresses.storeByCheckout') }}", {
        method: 'POST',
        body: formData,
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/json",
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('addressSelect');
            const option = document.createElement('option');
            option.value = data.address.id;
            option.textContent = `${data.address.street}, ${data.address.number} — ${data.address.district} — ${data.address.city}`;
            option.selected = true;
            select.appendChild(option);

            $('#newAddressForm').modal('hide');
        }
    });
}

</script>

