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
        require([
            "esri/Map",
            "esri/Basemap",
            "esri/views/SceneView",
            "esri/layers/GeoJSONLayer",
            "esri/popup/content/TextContent"
        ], (Map, Basemap, SceneView, GeoJSONLayer, TextContent) => {
            
            // Initialize the map with CodeTheMap basemap
            const map = new Map({
                basemap: new Basemap({
                    portalItem: {
                        id: "0560e29930dc4d5ebeb58c635c0909c9" // 3D Topographic Basemap
                    }
                }),
                ground: "world-elevation"
            });

            // Initialize the view
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
                },
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        position: "bottom-right",
                        breakpoint: false,
                        buttonEnabled: true
                    }
                }
            });

            // DPE color mapping
            const dpeColorMap = {
                'A': [46, 204, 113],  // Green
                'B': [39, 174, 96],   // Darker green
                'C': [241, 196, 15],  // Yellow
                'D': [230, 126, 34],  // Orange
                'E': [211, 84, 0],    // Dark orange
                'F': [231, 76, 60],   // Red
                'G': [192, 57, 43]    // Dark red
            };

            // Create GeoJSON layer for DPE data
            const dpeLayer = new GeoJSONLayer({
                url: "/api/buildings/geojson",
                copyright: "DPE Data",
                renderer: {
                    type: "simple",
                    symbol: {
                        type: "simple-marker",
                        size: 8,
                        color: [255, 0, 0], // Default color if no DPE class
                        outline: {
                            width: 0.5,
                            color: [255, 255, 255]
                        }
                    },
                    visualVariables: [{
                        type: "color",
                        field: "dpe_class",
                        stops: Object.entries(dpeColorMap).map(([dpeClass, color]) => ({
                            value: dpeClass,
                            color: color,
                            label: dpeClass
                        }))
                    }]
                },
                popupTemplate: {
                    title: "Building DPE: {dpe_class}",
                    content: [{
                        type: "text",
                        text: "Address: {address}"
                    }]
                }
            });

            // Add layer to map when view is ready
            view.when(() => {
                map.add(dpeLayer);
                console.log("DPE layer successfully loaded");
                
                // Debug: Check if features are loading
                dpeLayer.when(() => {
                    dpeLayer.queryFeatures().then((result) => {
                        console.log("DPE features loaded:", result.features.length);
                        if (result.features.length > 0) {
                            console.log("First feature:", result.features[0].attributes);
                            console.log("First feature geometry:", result.features[0].geometry);
                        } else {
                            console.warn("No DPE features loaded - checking API directly");
                            fetch("/api/buildings/geojson")
                                .then(res => res.json())
                                .then(data => console.log("Raw API response:", data))
                                .catch(err => console.error("API fetch error:", err));
                        }
                    }).catch(err => {
                        console.error("Feature query error:", err);
                    });
                });
            });

            // Error handling
            view.on("layerview-create-error", (event) => {
                console.error("LayerView error:", event.error);
            });
        });
    }
});