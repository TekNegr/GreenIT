<template>
  <div>
    <!-- Filtres -->
    <div id="filters">
      <label for="typeSelect">Type :</label>
      <select id="typeSelect" @change="reloadLayer">
        <option value="">Tous</option>
        <option value="building">Bâtiment</option>
        <option value="appartement">Appartement</option>
      </select>

      <label for="dpeSelect">DPE :</label>
      <select id="dpeSelect" @change="reloadLayer">
        <option value="">Tous</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
        <option value="E">E</option>
        <option value="F">F</option>
        <option value="G">G</option>
      </select>
    </div>

    <!-- Carte -->
    <div id="viewDiv" style="height: 90vh; width: 100%;"></div>
  </div>
</template>

<script>
export default {
  name: 'Map',
  mounted() {
    if (!window.require) {
      const arcgisScript = document.createElement('script');
      arcgisScript.src = 'https://js.arcgis.com/4.27/';
      arcgisScript.onload = this.initializeMap;
      document.head.appendChild(arcgisScript);
    } else {
      this.initializeMap();
    }
  },
  methods: {
    initializeMap() {
      window.require([
        "esri/Map",
        "esri/Basemap",
        "esri/views/SceneView",
        "esri/layers/GeoJSONLayer"
      ], (Map, Basemap, SceneView, GeoJSONLayer) => {
        this.map = new Map({
          basemap: new Basemap({
            portalItem: {
              id: "0560e29930dc4d5ebeb58c635c0909c9"
            }
          }),
          ground: "world-elevation"
        });

        this.view = new SceneView({
          container: "viewDiv",
          map: this.map,
          camera: {
            position: {
              longitude: 2.34,
              latitude: 48.88,
              z: 180
            },
            heading: 30,
            tilt: 65
          },
          popup: {
            dockEnabled: true,
            dockOptions: {
              position: "bottom-right",
              buttonEnabled: true
            }
          }
        });

        this.createLayer(GeoJSONLayer);
      });
    },

    buildLayerUrl() {
      const type = document.getElementById('typeSelect').value;
      const dpe = document.getElementById('dpeSelect').value;
      const params = new URLSearchParams();
      if (type) params.append('type', type);
      if (dpe) params.append('dpe', dpe);
      return "/api/buildings/geojson" + (params.toString() ? `?${params.toString()}` : '');
    },

    createLayer(GeoJSONLayer) {
      const dpeColorMap = {
        'A': [46, 204, 113],
        'B': [39, 174, 96],
        'C': [241, 196, 15],
        'D': [230, 126, 34],
        'E': [211, 84, 0],
        'F': [231, 76, 60],
        'G': [192, 57, 43]
      };

      if (this.dpeLayer) {
        this.map.remove(this.dpeLayer);
      }

      this.dpeLayer = new GeoJSONLayer({
        url: this.buildLayerUrl(),
        renderer: {
          type: "simple",
          symbol: {
            type: "simple-marker",
            size: 8,
            color: [255, 0, 0],
            outline: {
              width: 0.5,
              color: [255, 255, 255]
            }
          },
          visualVariables: [{
            type: "color",
            field: "dpe_class",
            stops: Object.entries(dpeColorMap).map(([value, color]) => ({
              value, color, label: value
            }))
          }]
        },
        popupTemplate: {
          title: "{nom}",
          content: [{
            type: "text",
            text: `
              DPE : {dpe_class} kWh/m²/an<br>
              GES : {gpe_class} kgCO₂/m²/an
            `
          }]
        }
      });

      this.map.add(this.dpeLayer);
    },

    reloadLayer() {
      this.createLayer(window.ESRI.GeoJSONLayer);
    }
  },
  data() {
    return {
      map: null,
      view: null,
      dpeLayer: null
    };
  }
}
</script>

<style scoped>
#filters {
  margin-bottom: 10px;
  padding: 10px;
  background: #f4f4f4;
  border-bottom: 1px solid #ccc;
}
</style>
