// JS of the CodeTheMap API 
document.addEventListener('DOMContentLoaded', function() {
    const viewDiv = document.getElementById('viewDiv');
    if (!viewDiv) {
        console.error('Error: viewDiv container not found');
        return;
    }

    if (!window.require) {
        const arcgisScript = document.createElement('script');
        arcgisScript.src = 'https://js.arcgis.com/4.27/';
        arcgisScript.onload = initializeMap;
        document.head.appendChild(arcgisScript);
    } else {
        initializeMap();
    }

    function initializeMap() {
        window.require([
            "esri/Map",
            "esri/Basemap",
            "esri/views/SceneView"
        ], function(Map, Basemap, SceneView) {
            const map = new Map({
                basemap: new Basemap({
                    portalItem: {
                        id: "0560e29930dc4d5ebeb58c635c0909c9" // 3D Topographic Basemap
                    }
                }),
                ground: "world-elevation"
            });

            const view = new SceneView({
                container: "viewDiv",
                map: map,
                camera: {
                    position: {
                        longitude: 2.340861136194503,
                        latitude: 48.88276594605576,
                        z: 178.8139155479148
                    },
                    heading: 29.620133897254565,
                    tilt: 65.59724234196116
                }
            });
        });
    }
});
