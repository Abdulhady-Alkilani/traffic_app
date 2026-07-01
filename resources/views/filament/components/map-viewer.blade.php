@php
    $record = $getRecord();
    $lat = $record?->latitude;
    $lng = $record?->longitude;
    $hasCoords = $lat && $lng;
    $autoLocate = $record === null;
    // Default to Hama coordinates if no record coords
    $mapLat = $hasCoords ? $lat : 35.13;
    $mapLng = $hasCoords ? $lng : 36.75;
    $isInteractive = $isInteractive ?? true;
    $mapId = 'map-' . uniqid();
@endphp

<div class="fi-fo-field-wrp">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-3 justify-between">
            <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3" for="{{ $mapId }}">
                <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    {{ __('messages.map') ?? 'الخريطة' }}
                </span>
            </label>
        </div>
        <div
            x-init="
                (function() {
                    var mId = '{{ $mapId }}';
                    var hasC = {{ $hasCoords ? 'true' : 'false' }};
                    var autoL = {{ $autoLocate ? 'true' : 'false' }};
                    var isInt = {{ $isInteractive ? 'true' : 'false' }};
                    // Use parseFloat to handle comma decimal formats safely
                    var mLat = parseFloat('{{ $mapLat }}'.replace(',', '.'));
                    var mLng = parseFloat('{{ $mapLng }}'.replace(',', '.'));

                    function loadLeaflet(callback) {
                        if (typeof window.L !== 'undefined') {
                            callback();
                            return;
                        }
                        if (!document.getElementById('leaflet-css')) {
                            var css = document.createElement('link');
                            css.id = 'leaflet-css';
                            css.rel = 'stylesheet';
                            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                            document.head.appendChild(css);
                        }
                        var script = document.createElement('script');
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.onload = callback;
                        document.head.appendChild(script);
                    }

                    function updateInputs(lat, lng) {
                        var latInputs = document.querySelectorAll('input[wire\\\\:model*=\'latitude\'], input[id*=\'latitude\'], input[name*=\'latitude\']');
                        var lngInputs = document.querySelectorAll('input[wire\\\\:model*=\'longitude\'], input[id*=\'longitude\'], input[name*=\'longitude\']');
                        latInputs.forEach(function(i) {
                            i.value = lat;
                            i.dispatchEvent(new Event('input', { bubbles: true }));
                            i.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                        lngInputs.forEach(function(i) {
                            i.value = lng;
                            i.dispatchEvent(new Event('input', { bubbles: true }));
                            i.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                        
                        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1')
                            .then(function(r) { return r.json(); })
                            .then(function(data) {
                                if (data && data.display_name) {
                                    var locInputs = document.querySelectorAll('input[wire\\\\:model*=\'location_text\'], input[id*=\'location_text\'], input[name*=\'location_text\']');
                                    locInputs.forEach(function(i) {
                                        i.value = data.display_name;
                                        i.dispatchEvent(new Event('input', { bubbles: true }));
                                        i.dispatchEvent(new Event('change', { bubbles: true }));
                                    });
                                }
                            }).catch(function(e) {});
                    }

                    loadLeaflet(function() {
                        setTimeout(function() {
                            var el = document.getElementById(mId);
                            if (!el || el._leaflet_id) return;

                            var map = L.map(el).setView([mLat, mLng], hasC ? 15 : 5);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap'
                            }).addTo(map);

                            var marker = L.marker([mLat, mLng], { draggable: isInt }).addTo(map);
                            
                            setTimeout(function() { map.invalidateSize(); }, 300);

                            if (isInt) {
                                marker.on('dragend', function(e) {
                                    var pos = e.target.getLatLng();
                                    updateInputs(pos.lat, pos.lng);
                                });

                                map.on('click', function(e) {
                                    marker.setLatLng(e.latlng);
                                    updateInputs(e.latlng.lat, e.latlng.lng);
                                });
                            }
                        }, 200);
                    });
                })();
            "
            class="grid gap-y-2"
        >
            <div id="{{ $mapId }}" style="height: 350px; width: 100%; border-radius: 0.5rem; z-index: 1;"></div>
        </div>
    </div>
</div>
