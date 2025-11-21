<!-- MODAL: EDITAR ENDEREÇO PELO GOOGLE MAPS -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog modal-xl">

        <form action="{{ route('addresses.update', $address->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Editar Endereço</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- AUTOCOMPLETE -->
                    <label class="fw-bold mb-1">Pesquisar endereço</label>
                    <input id="googleAutocompleteEdit" type="text" class="form-control form-control-lg mb-3"
                           placeholder="Digite um endereço..." />

                    <!-- MAPA -->
                    <div id="googleMapEdit" style="height: 420px; border-radius: 10px;"></div>

                    <hr class="my-4">

                    <!-- FORMULÁRIO -->
                    <h5 class="fw-bold mb-3">Dados do Endereço</h5>

                    <div class="row">

                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Rua</label>
                            <input type="text" id="street_edit" name="street" class="form-control"
                                   value="{{ $address->street }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Número</label>
                            <input type="text" id="number_edit" name="number" class="form-control"
                                   value="{{ $address->number }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Complemento</label>
                            <input type="text" id="complement_edit" name="complement" class="form-control"
                                   value="{{ $address->complement }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Bairro</label>
                            <input type="text" id="district_edit" name="district" class="form-control"
                                   value="{{ $address->district }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Cidade</label>
                            <input type="text" id="city_edit" name="city" class="form-control"
                                   value="{{ $address->city }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Estado</label>
                            <input type="text" id="state_edit" name="state" class="form-control"
                                   value="{{ $address->state }}" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">CEP</label>
                            <input type="text" id="zip_code_edit" name="zip_code" class="form-control"
                                   value="{{ $address->zip_code }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">País</label>
                            <input type="text" id="country_edit" name="country" class="form-control"
                                   value="{{ $address->country }}" required>
                        </div>

                    </div>

                    <!-- HIDDEN -->
                    <input type="hidden" id="latitude_edit" name="latitude" value="{{ $address->latitude }}">
                    <input type="hidden" id="longitude_edit" name="longitude" value="{{ $address->longitude }}">

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetEditModal()">Cancelar</button>

                    <button type="submit" class="btn btn-primary">
                        Atualizar Endereço
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places"></script>

<script>

let mapEdit;
let markerEdit;
let geocoderEdit;
let autocompleteEdit;
let currentAddress = null; // referência ao endereço sendo editado

document.addEventListener("DOMContentLoaded", function () {

    let originalData = null;
    let marker = null; // precisa existir globalmente e já ter sido criado antes
    const modal = document.getElementById("editAddressModal");

    modal.addEventListener("shown.bs.modal", () => {
        setTimeout(initGoogleMapEdit, 300);
    });

});

function initGoogleMapEdit() {

    if (mapEdit) {
        google.maps.event.trigger(mapEdit, "resize");
        return;
    }

    geocoderEdit = new google.maps.Geocoder();

    const initialLat = parseFloat(document.getElementById("latitude_edit").value);
    const initialLng = parseFloat(document.getElementById("longitude_edit").value);

    const centerLatLng = {
        lat: initialLat || -23.5505,
        lng: initialLng || -46.6333
    };

    mapEdit = new google.maps.Map(document.getElementById("googleMapEdit"), {
        center: centerLatLng,
        zoom: initialLat ? 17 : 14,
        streetViewControl: false,
        mapTypeControl: false,
        fullscreenControl: false
    });

    markerEdit = new google.maps.Marker({
        position: centerLatLng,
        map: mapEdit,
        draggable: true
    });

    markerEdit.addListener("dragend", () => {
        const pos = markerEdit.getPosition();
        updateAddressFromLatLngEdit(pos.lat(), pos.lng());
    });

    mapEdit.addListener("click", (e) => {
        markerEdit.setPosition(e.latLng);
        updateAddressFromLatLngEdit(e.latLng.lat(), e.latLng.lng());
    });

    initAutocompleteEdit();
}

/* AUTOCOMPLETE */
function initAutocompleteEdit() {

    const input = document.getElementById("googleAutocompleteEdit");

    autocompleteEdit = new google.maps.places.Autocomplete(input, {
        fields: ["address_components", "geometry"],
        componentRestrictions: { country: "br" }
    });

    autocompleteEdit.addListener("place_changed", () => {

        const place = autocompleteEdit.getPlace();
        if (!place.geometry) return;

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        mapEdit.setCenter({ lat, lng });
        mapEdit.setZoom(18);

        markerEdit.setPosition({ lat, lng });

        fillEditFields(place.address_components);

        document.getElementById("latitude_edit").value = lat;
        document.getElementById("longitude_edit").value = lng;
    });
}

/* REVERSE GEOCODING */
function updateAddressFromLatLngEdit(lat, lng) {

    geocoderEdit.geocode({ location: { lat, lng } }, (results, status) => {

        if (status === "OK" && results[0]) {

            fillEditFields(results[0].address_components);

            document.getElementById("latitude_edit").value = lat;
            document.getElementById("longitude_edit").value = lng;
        }
    });
}

/* HELPERS */
function getPartEdit(components, type) {
    let obj = components.find(c => c.types.includes(type));
    return obj ? obj.long_name : "";
}

function fillEditFields(components) {

    document.getElementById("street_edit").value = getPartEdit(components, "route");
    document.getElementById("number_edit").value = getPartEdit(components, "street_number");

    const districtValue = getPartEdit(components, "sublocality_level_1") ||
                          getPartEdit(components, "sublocality");

    document.getElementById("district_edit").value = districtValue;

    document.getElementById("city_edit").value =
        getPartEdit(components, "administrative_area_level_2");

    document.getElementById("state_edit").value =
        getPartEdit(components, "administrative_area_level_1");

    document.getElementById("zip_code_edit").value =
        getPartEdit(components, "postal_code");

    document.getElementById("country_edit").value =
        getPartEdit(components, "country");
}

function openEditModal(address) {

    currentAddress = address;

    document.getElementById('street_edit').value      = address.street ?? "";
    document.getElementById('number_edit').value      = address.number ?? "";
    document.getElementById('complement_edit').value  = address.complement ?? "";
    document.getElementById('district_edit').value    = address.district ?? "";
    document.getElementById('city_edit').value        = address.city ?? "";
    document.getElementById('state_edit').value       = address.state ?? "";
    document.getElementById('zip_code_edit').value    = address.zip_code ?? "";
    document.getElementById('country_edit').value     = address.country ?? "Brasil";
    document.getElementById('latitude_edit').value    = address.latitude;
    document.getElementById('longitude_edit').value   = address.longitude;

    if (marker) {
        marker.setLatLng([address.latitude, address.longitude]);
        map.setView([address.latitude, address.longitude], 16);
    }
}

function resetEditModal() {
    // console.log(currentAddress, "Current adress");
    if (!currentAddress) return;

    document.getElementById('street').value      = currentAddress.street ?? "";
    document.getElementById('number').value      = currentAddress.number ?? "";
    document.getElementById('complement').value  = currentAddress.complement ?? "";
    document.getElementById('district').value    = currentAddress.district ?? "";
    document.getElementById('city').value        = currentAddress.city ?? "";
    document.getElementById('state').value       = currentAddress.state ?? "";
    document.getElementById('zip_code').value    = currentAddress.zip_code ?? "";
    document.getElementById('country').value     = currentAddress.country ?? "Brasil";
    document.getElementById('latitude').value    = currentAddress.latitude;
    document.getElementById('longitude').value   = currentAddress.longitude;

    if (marker) {
        marker.setLatLng([currentAddress.latitude, currentAddress.longitude]);
    }
}

</script>
