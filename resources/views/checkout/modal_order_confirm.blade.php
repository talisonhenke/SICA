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
                                <option value="{{ $address }}"
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
                            <input type="text" class="form-control" id="new_street" name="order_address[street]">
                        </div>

                        <div class="row">
                            <div class="col-4 mb-2">
                                <label class="form-label">Número</label>
                                <input type="text" class="form-control" id="new_number" name="order_address[number]">
                            </div>
                            <div class="col-8 mb-2">
                                <label class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="new_district" name="order_address[district]">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="new_city" name="order_address[city]">
                            </div>

                            <div class="col-6 mb-2">
                                <label class="form-label">CEP</label>
                                <input type="text" class="form-control" id="new_zipcode" name="order_address[zip_code]">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label">Estado</label>
                                <input type="text" class="form-control" id="new_state" name="order_address[state]">
                            </div>

                            <div class="col-6 mb-2">
                                <label class="form-label">País</label>
                                <input type="text" class="form-control" id="new_country" name="order_address[country]">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Complemento (opcional)</label>
                            <input type="text" class="form-control" id="new_complement" name="order_address[complement]">
                        </div>

                        <input type="hidden" id="new_lat" name="order_address[latitude]">
                        <input type="hidden" id="new_lng" name="order_address[longitude]">

                        <button type="button" class="btn btn-primary w-100 mt-2" id="btnSaveAddress" onclick="saveNewAddress()">
                            Salvar Endereço
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="btnCancelNewAddress">
                            Cancelar
                        </button>


                    </div>

                </div>



           <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnMainCancel" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-success" id="btnMainConfirm">
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

    const collapseEl = document.getElementById("newAddressForm");
    const btnMainCancel = document.getElementById("btnMainCancel");
    const btnMainConfirm = document.getElementById("btnMainConfirm");
    const btnCancelNewAddress = document.getElementById("btnCancelNewAddress");

    // Quando abrir o formulário de novo endereço
    collapseEl.addEventListener("show.bs.collapse", () => {
        btnMainCancel.style.display = "none";
        btnMainConfirm.style.display = "none";
    });

    // Quando fechar o formulário
    collapseEl.addEventListener("hide.bs.collapse", () => {
        btnMainCancel.style.display = "inline-block";
        btnMainConfirm.style.display = "inline-block";
    });

    // Ação do botão CANCELAR interno
    btnCancelNewAddress.addEventListener("click", () => {
        const collapseInstance = bootstrap.Collapse.getOrCreateInstance(collapseEl);
        collapseInstance.hide();
    });
});


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

    // ⭐ NOVOS CAMPOS
    document.getElementById("new_state").value    = getPart(comp, "administrative_area_level_1");
    document.getElementById("new_country").value  = getPart(comp, "country");
}

</script>

<script>
function saveNewAddress() {

    const payload = {
        street:     document.getElementById('new_street').value,
        number:     document.getElementById('new_number').value,
        district:   document.getElementById('new_district').value,
        city:       document.getElementById('new_city').value,
        state:      document.getElementById('new_state').value,
        country:    document.getElementById('new_country').value,
        zip_code:   document.getElementById('new_zipcode').value,
        latitude:   document.getElementById('new_lat').value,
        longitude:  document.getElementById('new_lng').value,
        complement: document.getElementById('new_complement').value
    };

    fetch("{{ route('addresses.storeByCheckout') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const text = await response.text();

        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("Resposta não-JSON:", text);
            throw new Error("Controller retornou HTML ao invés de JSON.");
        }
    })
    .then(data => {
        console.log("Retorno do Laravel:", data);

        if (!data.success) {
            alert("Erro ao cadastrar o endereço.");
            return;
        }

        const address = data.address;

        // -------------------------------------------------------
        // 1. ADICIONA O NOVO ENDEREÇO AO SELECT
        // -------------------------------------------------------
        const select = document.getElementById("addressSelect");

        const option = document.createElement("option");
        option.value = address.id;
        document.getElementById('order_address_json').value = JSON.stringify(address);
        console.log("Tste endereço" + JSON.stringify(address));
        option.textContent =
            `${address.street}, ${address.number} — ${address.district} — ${address.city}`;

        select.appendChild(option);

        // -------------------------------------------------------
        // 2. JÁ SELECIONA O ENDEREÇO ADICIONADO
        // -------------------------------------------------------
        select.value = address.id;

        // -------------------------------------------------------
        // 3. FECHA O FORMULÁRIO DE NOVO ENDEREÇO
        // -------------------------------------------------------
        const collapse = bootstrap.Collapse.getOrCreateInstance(
            document.getElementById("newAddressForm")
        );
        collapse.hide();

        // Restaurar botões principais
        document.getElementById("btnMainCancel").style.display = "inline-block";
        document.getElementById("btnMainConfirm").style.display = "inline-block";

        // -------------------------------------------------------
        // (Opcional) limpar os campos do formulário
        // -------------------------------------------------------
        document.getElementById("new_street").value = "";
        document.getElementById("new_number").value = "";
        document.getElementById("new_district").value = "";
        document.getElementById("new_city").value = "";
        document.getElementById("new_state").value = "";
        document.getElementById("new_country").value = "";
        document.getElementById("new_zipcode").value = "";
        document.getElementById("new_complement").value = "";
        document.getElementById("new_lat").value = "";
        document.getElementById("new_lng").value = "";

        alert("Endereço cadastrado com sucesso!");

    })
    .catch(error => {
        console.error(error);
        alert("Erro ao cadastrar o endereço.");
    });
}

</script>

