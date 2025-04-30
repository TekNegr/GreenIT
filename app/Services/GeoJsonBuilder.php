class GeoJsonBuilder {
    public static function fromCollection($collection) {
        return [
            'type' => 'FeatureCollection',
            'features' => $collection->map(fn($item) => [
                'type' => 'Feature',
                'geometry' => [...],
                'properties' => [...]
            ]),
        ];
    }
}
