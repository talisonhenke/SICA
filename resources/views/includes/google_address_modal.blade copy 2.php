<!-- MODAL: SELECIONAR ENDEREÇO PELO GOOGLE MAPS -->
{{-- <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places"></script> --}}

<script type="module" src="https://ajax.googleapis.com/ajax/libs/@googlemaps/extended-component-library/0.6.11/index.min.js">
</script>

<script async 
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places">
</script>

<div class="modal fade" id="googleAddressModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form action="{{ route('addresses.store') }}" method="POST">
            @csrf
            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-body">
                    
                    <label class="fw-bold mb-1">Pesquisar endereço</label>
                    <div class="mb-3"> 
                        <gmpx-place-picker id="placePicker" 
                                           placeholder="Digite um endereço, rua, bairro, estabelecimento..."
                                           style="--gmpx-place-picker-input-min-height: 50px;">
                        </gmpx-place-picker>
                    </div>

                    <gmp-map id="gmpMap" center="-31.766552285723456, -52.342316153597814" zoom="15" map-id="DEMO_MAP_ID" 
                             style="height: 420px; border-radius: 10px;">
                        
                        <gmp-advanced-marker id="mapMarker" slot="markers" position="-31.766552285723456, -52.342316153597814" gmp-draggable="true"></gmp-advanced-marker>
                    </gmp-map>

                    <hr class="my-4">
                    </div>
                </div>
        </form>
    </div>
</div>

<script>
// =========================================================
// VARIÁVEIS E FUNÇÕES DE UTILIDADE (MANTIDAS)
// =========================================================
const componentMap = {
    'street_number': 'number', 'route': 'street',
    'sublocality': 'district', 'sublocality_level_1': 'district', 
    'administrative_area_level_2': 'city', 'administrative_area_level_1': 'state',
    'country': 'country', 'postal_code': 'zip_code',
};

// FUNÇÃO DE PREENCHIMENTO DOS CAMPOS (Completa)
function fillAddressFields(components, lat = null, lng = null) {
    
    Object.values(componentMap).forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    
    const getPart = (comps, type) => {
        const obj = comps.find(c => c.types.includes(type));
        return obj ? obj.long_name : "";
    };

    document.getElementById("street").value = getPart(components, "route");
    document.getElementById("number").value = getPart(components, "street_number");
    document.getElementById("city").value = getPart(components, "administrative_area_level_2");
    document.getElementById("state").value = getPart(components, "administrative_area_level_1");
    document.getElementById("zip_code").value = getPart(components, "postal_code");
    document.getElementById("country").value = getPart(components, "country");
    document.getElementById("district").value = getPart(components, "sublocality") || getPart(components, "sublocality_level_1");

    if (lat !== null && lng !== null) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }
}


// =========================================================
// FUNÇÃO PRINCIPAL DE INICIALIZAÇÃO (CALLBACK DO GOOGLE MAPS)
// RESTAURADA para ASYNC
// =========================================================
async function initMap() { 
    
    // 1. GARANTE QUE O WEB COMPONENT ESTEJA DEFINIDO ANTES DE TENTAR ACESSAR SUAS PROPRIEDADES
    await customElements.whenDefined('gmp-map'); 
    
    // 2. OBTENÇÃO DOS ELEMENTOS E CONFIGURAÇÃO
    const gmpMap = document.getElementById('gmpMap');
    const mapMarker = document.getElementById('mapMarker');
    const placePicker = document.getElementById('placePicker');
    const modal = document.getElementById("googleAddressModal");
    const geocoder = new google.maps.Geocoder();
    const fallbackCenter = { lat: -23.5505, lng: -46.6333 }; 


    // 3. FUNÇÃO DE POSIÇÃO INICIAL (Usa LatLngLiteral e previne o erro)
    const setInitialPosition = (lat, lng, zoom = 15) => {
        const validLat = parseFloat(lat);
        const validLng = parseFloat(lng);
        
        if (!isFinite(validLat) || !isFinite(validLng) || !gmpMap || !mapMarker) {
            console.error("Coordenadas inválidas ou Componentes não prontos.");
            return;
        }

        const positionLiteral = { lat: validLat, lng: validLng };

        // 🎯 Move o mapa e o marcador usando o LatLngLiteral (formato corrigido)
        gmpMap.center = positionLiteral;
        gmpMap.gestureHandling = 'none';
        gmpMap.zoom = zoom;
        mapMarker.position = positionLiteral;
        
        document.getElementById('latitude').value = validLat;
        document.getElementById('longitude').value = validLng;
    };


    // 4. LÓGICA DE GEOLOCALIZAÇÃO
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => setInitialPosition(pos.coords.latitude, pos.coords.longitude, 16),
            (error) => {
                 // Executa o fallback se a geolocalização falhar
                 setInitialPosition(fallbackCenter.lat, fallbackCenter.lng, 15);
                 console.error("Erro na Geolocalização. Usando fallback.", error.message);
            }
        );
    } else {
        // Executa o fallback se o navegador não suportar
        setInitialPosition(fallbackCenter.lat, fallbackCenter.lng, 15);
    }

    // =========================================================
    // 5. ANEXAÇÃO DOS LISTENERS (AGORA NO EVENTO DE PRONTIDÃO DO MAPA)
    // =========================================================
    
    // Usa um evento interno do mapa que garante que o componente está pronto para interações
    gmpMap.addEventListener('gmp-bounds_changed', function attachListenersOnce() {
        
        // Remove este listener para que o código só seja executado uma vez
        gmpMap.removeEventListener('gmp-bounds_changed', attachListenersOnce); 

        // ----------------------------------------------------
        // 5.1. Evento do Modal (Mantido)
        modal.addEventListener("shown.bs.modal", () => {
            if (gmpMap && gmpMap.requestLayout) gmpMap.requestLayout();
        });

        // 5.2. EVENTO: SELEÇÃO DE SUGESTÃO (PLACE PICKER)
        placePicker.addEventListener('gmpx-place-select', (e) => {
            const place = e.detail.place;
            if (place && place.geometry) {
                const { lat, lng } = place.geometry.location;
                setInitialPosition(lat, lng, 18); 
                // fillAddressFields(place.addressComponents, lat, lng);
            }
        });

        // 5.3. EVENTO: CLIQUE NO MAPA (Agora deve disparar)
        gmpMap.addEventListener('gmp-click', async (e) => {
            const lat = e.detail.latLng.lat;
            const lng = e.detail.latLng.lng;
            
            console.log(`[CLICK EVENTO]: Nova posição: ${lat}, ${lng}`); // Debug
            
            const positionLiteral = { lat, lng };
            mapMarker.position = positionLiteral; 
            gmpMap.center = positionLiteral; 
            
            const response = await geocoder.geocode({ location: positionLiteral });
            if (response.results && response.results.length > 0) {
                // fillAddressFields(response.results[0].address_components, lat, lng);
            }
        });

        // 5.4. EVENTO: ARRASTAR MARCADOR (Agora deve disparar)
        mapMarker.addEventListener('gmp-advanced-marker-dragend', async (e) => {
            console.log("[DRAG END]: Marcador solto."); // Debug
            
            const position = mapMarker.position; 
            const lat = typeof position.lat === 'function' ? position.lat() : position.lat;
            const lng = typeof position.lng === 'function' ? position.lng() : position.lng;
            
            const positionLiteral = { lat, lng };

            const response = await geocoder.geocode({ location: positionLiteral });
            if (response.results && response.results.length > 0) {
                // fillAddressFields(response.results[0].address_components, lat, lng);
            }
        });

    }, false); // O 'false' no final é importante para que o listener seja acionado na fase de bubble
}
</script>