@php
    $lat = $getRecord()?->latitude ?? 0;
    $lng = $getRecord()?->longitude ?? 0;
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
        <div class="grid gap-y-2">
            @if($lat && $lng)
                <div id="{{ $mapId }}" style="height: 300px; width: 100%; border-radius: 0.5rem; z-index: 1;"></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        if (!document.getElementById('leaflet-css')) {
                            var css = document.createElement('link');
                            css.id = 'leaflet-css';
                            css.rel = 'stylesheet';
                            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                            document.head.appendChild(css);
                        }
                        
                        if (!window.L) {
                            var script = document.createElement('script');
                            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                            script.onload = initMap;
                            document.head.appendChild(script);
                        } else {
                            initMap();
                        }

                        function initMap() {
                            setTimeout(function() {
                                var mapElement = document.getElementById('{{ $mapId }}');
                                if (!mapElement) return;
                                var map = L.map(mapElement).setView([{{ $lat }}, {{ $lng }}], 15);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '© OpenStreetMap'
                                }).addTo(map);
                                L.marker([{{ $lat }}, {{ $lng }}]).addTo(map);
                            }, 500); // Small delay to ensure the container has size
                        }
                    });
                </script>
            @else
                <div class="text-sm text-gray-500">
                    لا يوجد إحداثيات صالحة لعرض الخريطة.
                </div>
            @endif
        </div>
    </div>
</div>
