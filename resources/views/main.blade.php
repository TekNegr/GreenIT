<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/CTM.js'
    ])
    @livewireStyles
    @livewireScripts

    <link rel="stylesheet" href="https://js.arcgis.com/4.27/esri/themes/light/main.css">
    
    <title>3NERGY - MAP</title>
    <style>
        .main-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        .sidebar {
            width: 300px;
            height: 100%;
        }
        .map-container {
            flex: 1;
            position: relative;
        }
        #viewDiv {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sidebar">
            @livewire('sidebar')
        </div>
        <div class="map-container">
            <div id="viewDiv"></div>
        </div>
    </div>
</body>
</html>
