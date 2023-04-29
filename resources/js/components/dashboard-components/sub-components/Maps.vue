<template>
	<div>
		<v-switch
			v-model="enable"
			label="Enable GPS on this Account"
		></v-switch>
			<div v-if="enable">
				<v-card-text>Latitude: {{marker.position.lat}}</v-card-text>
				<v-card-text>Longitude: {{marker.position.lat}}</v-card-text>
			</div>
		<div style="height: 550px; width: 100%" >
			
			<LMap
				ref="map"
				v-if="enable"
				:zoom="zoom"
				:center="center"
				@update:center="centerMap"
			>
				<LMarker
					:visible="true"
					:draggable="marker.draggable"
					:lat-lng.sync="marker.position"
				/>
				<LTileLayer :url="url" :attribution="attribution"></LTileLayer>

				<LGeoJson v-if="enable" :geojson="map_json" />
				
				<VGeoSearch :options="geoSearchOptions" />
			</LMap>
		</div>
	</div>
</template>

<script>
import L from "leaflet";
import {
	LMap,
	LTileLayer,
	LMarker,
	LPopup,
	LTooltip,
	LGeoJson,
} from "vue2-leaflet";
import { OpenStreetMapProvider } from "leaflet-geosearch";
import VGeoSearch from "vue2-leaflet-geosearch";
import apiRoutes from "../../../apiRoutes";
export default {
	data: () => ({
		enable: false,
		url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
		attribution:
			'&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
		center: L.latLng(8.4777777, 124.64258),
		zoom: 13,
		map_json: null,
		geoSearchOptions: {
			provider: new OpenStreetMapProvider(),
		},
		marker: {
			id: "m1",
			position:  { lat: 8.4777777, lng: 124.64258 },
			tooltip: "Your location",
			draggable: true,
			visible: true,
		},
	}),
	components: {
		LMap,
		LTileLayer,
		LMarker,
		LPopup,
		LTooltip,
		LGeoJson,
		VGeoSearch,
	},
	mounted() {
		this.initialize();
	},
	methods: {
		centerMap(center) {
			this.center = center;
		},

		async initialize(){
			const response = await apiRoutes.getlocation()
			if(response === ""){
				this.enable = false;
				return false;
			}
			this.marker.position.lat = parseFloat(response.latitude);
			this.marker.position.lng = parseFloat(response.longitude);
			this.enable = Boolean(response.status);
			this.centerMap(L.latLng(parseFloat(response.latitude), parseFloat(response.longitude)))
		}
	},
	watch: {
		'marker.position':function(){
			const latitude = this.marker.position.lat;
			const longitude = this.marker.position.lng;
			apiRoutes.updatelocation({latitude, longitude});
		},
		enable: function(){
			const status = this.enable;
			apiRoutes.updatelocation({status});
		}
	},
	async created() {
		const geojson = await fetch(
			"https://gist.githubusercontent.com/eloijj/fc1a8ae5682efbc6d459f0fa018e2253/raw/bdd6b986901b246a50766588171b37e17426b180/map.geojson"
		);
		const map_data = await geojson.json();
		this.map_json = map_data;
	},
};
</script>
