@push('head')
    <link rel="stylesheet" href="/vendor/leaflet/leaflet.css">
@endpush
@push('scripts')
    <script src="/vendor/leaflet/leaflet.js"></script>
    <script>
        window.KINGTAG = {
            center: [{{ config('kingtag.map_center.lat') }}, {{ config('kingtag.map_center.lng') }}],
            graffitisUrl: @json(route('map.graffitis')),
            nearbyUrl: @json(route('map.nearby')),
        };
    </script>
    <script src="/js/map.js?v={{ filemtime(public_path('js/map.js')) }}"></script>
@endpush
