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
            "esri/popup/content/TextContent",
            "esri/geometry/support/webMercatorUtils"
        ], (Map, Basemap, SceneView, GeoJSONLayer, TextContent, webMercatorUtils) => {
            
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

            /*
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
            */

            // Add layer to map when view is ready
            view.when(() => {
                // map.add(dpeLayer);
                // console.log("DPE layer successfully loaded");

                // Send current BBox to backend API to fetch appartements
                const sendBBoxToBackend = () => {
                    const extent = view.extent;
                    if (!extent) {
                        console.warn("View extent not available");
                        return;
                    }
                    const geographicExtent = webMercatorUtils.webMercatorToGeographic(extent);

                    const bbox = [
                        geographicExtent.xmin,
                        geographicExtent.ymin,
                        geographicExtent.xmax,
                        geographicExtent.ymax
                    ].join(',');

                    console.log("Sending BBox to backend:", bbox);
                    fetch('/api/fetch-appartements', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ bbox })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('ImportDpeData job dispatched:', data);
                    })
                    .catch(error => {
                        console.error('Error dispatching ImportDpeData job:', error);
                    });
                    console.log("BBox sent to backend");
                };

                // Send BBox on initial load
                sendBBoxToBackend();

                // Optionally, send BBox on view extent change (debounced)
                let debounceTimeout;
                view.watch('extent', () => {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(() => {
                        sendBBoxToBackend();
                    }, 1000);
                });

                // Debug: Check if features are loading
                /*
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
                */
            });

            // Error handling
            view.on("layerview-create-error", (event) => {
                console.error("LayerView error:", event.error);
            });

            // Remove this entire view.watch('camera') block to avoid duplicate fetch calls
            /*
            view.watch('camera', (camera) => {
                const position = {
                    longitude: camera.position.longitude,
                    latitude: camera.position.latitude,
                    altitude: camera.position.z,
                    heading: camera.heading,
                    tilt: camera.tilt
                };

                const metersToDegreesLat = (meters) => meters / 11100000;
                const metersToDegreesLon = (meters, latitude) => meters / (11100000 * Math.cos(latitude * Math.PI / 180));

                const altitude = camera.position.z; // in meters
                const fovRadians = camera.fov * Math.PI / 180;
                const rangeM = Math.tan(fovRadians) * altitude;

                const rangeLat = metersToDegreesLat(rangeM);
                const rangeLon = metersToDegreesLon(rangeM, camera.position.latitude);

                const bbox = [
                    camera.position.longitude - rangeLon,
                    camera.position.latitude - rangeLat,
                    camera.position.longitude + rangeLon,
                    camera.position.latitude + rangeLat
                ].join(',');
                console.log("Camera position:", position);

                // if (range < 1000) {
                //     view.popup.open({
                //         title: "Camera Position",
                //         content: `Longitude: ${position.longitude}, Latitude: ${position.latitude}, Altitude: ${position.altitude}, Heading: ${position.heading}, Tilt: ${position.tilt}`,
                //         location: camera.position
                //     });
                // }
            
                fetch('/api/fetch-appartement', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(bbox)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('SceneView position logged:', data);
                })
                .catch(error => {
                    console.error('Error logging SceneView position:', error);
                });
                console.log("Camera range:", rangeM);
                console.log("Camera BBox:", bbox);
                console.log("Camera position:", position);
                console.log("Camera heading:", camera.heading);
                console.log("Camera tilt:", camera.tilt);
                console.log("Camera fov:", camera.fov);
                
            });
            */
        });
    }
});