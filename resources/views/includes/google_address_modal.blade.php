<!-- MODAL: SELECIONAR ENDEREÇO PELO GOOGLE MAPS -->
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
        setTimeout(initGoogleMap, 300);
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

    initAutocomplete();
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
