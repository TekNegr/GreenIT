document.addEventListener('DOMContentLoaded', function() {
    const viewDiv = document.getElementById('viewDiv');
    if (!viewDiv) {
        console.error('Error: viewDiv container not found');
        return;
    }

    if (!window.require) {
        console.log('Loading ArcGIS API...');
        const arcgisScript = document.createElement('script');
        arcgisScript.src = 'https://js.arcgis.com/4.27/';
        arcgisScript.onload = initializeMap;
        document.head.appendChild(arcgisScript);
    } else {
        initializeMap();
    }

    function initializeMap() {
        console.log('Initializing map...');
        
        window.require([
            "esri/config",
            "esri/Map", 
            "esri/views/SceneView",
            "esri/layers/SceneLayer",
            "esri/layers/GraphicsLayer",
            "esri/Graphic",
            "esri/geometry/Point",
            "esri/symbols/SimpleMarkerSymbol"
        ], function(esriConfig, Map, SceneView, SceneLayer, GraphicsLayer, Graphic, Point, SimpleMarkerSymbol) {
            esriConfig.assetsPath = "https://js.arcgis.com/4.27/";
            
            // Try alternative buildings layer
            const buildingsLayer = new SceneLayer({
                url: "https://tiles.arcgis.com/tiles/V6ZHFr6zdgNZuVG0/arcgis/rest/services/Paris_3D_Buildings/SceneServer/layers/0",
                popupEnabled: true
            });

            // Fallback graphics layer if buildings fail
            const fallbackLayer = new GraphicsLayer();
            
            const map = new Map({
                basemap: "streets-navigation-vector",
                ground: "world-elevation",
                layers: [buildingsLayer, fallbackLayer]
            });

            const view = new SceneView({
                container: "viewDiv",
                map: map,
                camera: {
                    position: {
                        longitude: 2.340861,
                        latitude: 48.882765,
                        z: 178.813915
                    },
                    heading: 29.620133,
                    tilt: 65.597242
                },
                qualityProfile: "high",
                environment: {
                    lighting: {
                        directShadowsEnabled: true
                    }
                },
                ui: {
                    components: []
                }
            });

            buildingsLayer.when(() => {
                console.log("3D Buildings layer loaded successfully");
            }).catch(err => {
                console.warn("3D Buildings failed, adding fallback markers:", err);
                // Add simple markers as fallback
                const point = new Point({
                    longitude: 2.340861,
                    latitude: 48.882765
                });
                const marker = new Graphic({
                    geometry: point,
                    symbol: new SimpleMarkerSymbol({
                        color: [226, 119, 40],
                        outline: {
                            color: [255, 255, 255],
                            width: 2
                        }
                    })
                });
                fallbackLayer.add(marker);
            });

            view.when(() => {
                console.log("Map view is ready");
            }).catch(err => {
                console.error("Map view error:", err);
            });
        });
    }
});
