<template>
    <card class="flex flex-col items-center justify-center">
        <div id='map'></div>
        <button v-if="!isHiddenAttributes" class="show_attributes" v-on:click="isHiddenAttributes = !isHiddenAttributes">Hide Attributes</button>
        <button v-else class="show_attributes" v-on:click="isHiddenAttributes = !isHiddenAttributes">Show Attributes</button>
        <div v-if="!isHiddenAttributes" id="attributes" v-html="attributes"></div>
    </card>
</template>

<script>
import mapboxgl from "mapbox-gl";
import MapboxGeocoder from "@mapbox/mapbox-gl-geocoder";
import * as Cookie from "js-cookie";
import queryString from "query-string";

import "../../../node_modules/mapbox-gl/dist/mapbox-gl.css";
import "../../../node_modules/@mapbox/mapbox-gl-geocoder/dist/mapbox-gl-geocoder.css";

const API_KEY =
  "pk.eyJ1IjoiYXNzZCIsImEiOiJjam4waHV1M2kwYXRpM3VwYzYyaTV6em5wIn0.JuV5MaCB2t0sdAgwxrJVbQ";

export default {
  props: ['businessid','lat', 'lng'],
  data() {

    return {
      isHiddenAttributes: false,
      attributes: '',
      totalBusinesses: 0,
      totalReviews: 0,
      totalImages: 0,
      map: null,
        businessId:null
    };
  },
  mounted() {
      this.businessId = this.businessid;
    this.init();
  },
  methods: {
    init() {
      this.createMap();
    },
    setCookie(name, value, hours = 1) {
      Cookie.set(name, value, { expires: 1 });
    },
    getCookie(name) {
      return Cookie.get(name);
    },
    getCenter() {
        return {
          zoom: 7,
          center: {
            lat: this.lat,
            lng: this.lng
          }
        };
    },
    getGeoJsonUrl() {
      return '/api/v1/businesses/geo-json/' + this.businessId;
    },
    createMap() {
      mapboxgl.accessToken = API_KEY;

      this.map = new mapboxgl.Map({
        container: "map",
        style: "mapbox://styles/mapbox/streets-v9",
        minZoom: 4,
        center: [this.getCenter().center.lng, this.getCenter().center.lat],
        zoom: this.getCenter().zoom
      });


      this.addClusters();
    },
    addClusters: function() {
      let map = this.map;
      map.addControl(new mapboxgl.NavigationControl());
      map.addControl(
        new MapboxGeocoder({
          accessToken: mapboxgl.accessToken
        })
      );

      this.map.on("load", () => {
        map.addSource("places", {
          type: "geojson",
          data: this.getGeoJsonUrl(),
          cluster: true,
          clusterMaxZoom: 14,
          clusterRadius: 50
        });

        map.addLayer({
          id: "clusters",
          type: "circle",
          source: "places",
          filter: ["has", "point_count"],
          paint: {
            "circle-color": [
              "step",
              ["get", "point_count"],
              "#51bbd6",
              100,
              "#f1f075",
              750,
              "#f28cb1"
            ],
            "circle-radius": [
              "step",
              ["get", "point_count"],
              20,
              100,
              30,
              750,
              40
            ]
          }
        });
        map.addLayer({
          id: "cluster-count",
          type: "symbol",
          source: "places",
          filter: ["has", "point_count"],
          layout: {
            "text-field": "{point_count_abbreviated}",
            "text-font": ["DIN Offc Pro Medium", "Arial Unicode MS Bold"],
            "text-size": 12
          }
        });
        map.addLayer({
          id: "unclustered-point",
          type: "circle",
          source: "places",
          filter: ["!", ["has", "point_count"]],
          paint: {
            "circle-color": "#da0913",
            "circle-radius": 4,
            "circle-stroke-width": 1,
            "circle-stroke-color": "#fff"
          }
        });
        map.on("click", "clusters", function(e) {
          var features = map.queryRenderedFeatures(e.point, {
            layers: ["clusters"]
          });
          var clusterId = features[0].properties.cluster_id;
          map
            .getSource("places")
            .getClusterExpansionZoom(clusterId, function(err, zoom) {
              if (err) return;

              map.easeTo({
                center: features[0].geometry.coordinates,
                zoom: zoom
              });
            });
        });
        map.on("mouseenter", "clusters", function() {
          map.getCanvas().style.cursor = "pointer";
        });
        map.on("mouseleave", "clusters", function() {
          map.getCanvas().style.cursor = "";
        });
        map.on("mouseenter", "unclustered-point", function() {
          map.getCanvas().style.cursor = "pointer";
        });
        map.on("click", "unclustered-point", function(e) {
          var coordinates = e.features[0].geometry.coordinates.slice();
          var description = e.features[0].properties.name;

          while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
            coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
          }

          new mapboxgl.Popup()
            .setLngLat(coordinates)
            .setHTML(description)
            .addTo(map);
        });

        map.on(
          "zoomend",
          function() {
            this.setCookie(
              "map_position",
              JSON.stringify({
                zoom: map.getZoom(),
                center: map.getCenter()
              })
            );

          }.bind(this)
        );

        map.on(
          "dragend",
          function() {
            this.setCookie(
              "map_position",
              JSON.stringify({
                zoom: map.getZoom(),
                center: map.getCenter()
              })
            );

          }.bind(this)
        );
      });
    }
  }
};
</script>
