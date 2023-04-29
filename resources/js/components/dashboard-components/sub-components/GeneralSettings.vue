<template>
    <div>
        <v-card-text text class="text-h5">App settings</v-card-text>
        <v-container>
            <v-row>
                <v-col>
                    <v-switch
                        v-model="config.idleCheck"
                        label="Check of Inactivity"
                    ></v-switch>

                    <div v-if="config.idleCheck">
                        <v-slider
                            v-model="config.idleMinutes.val"
                            :label="config.idleMinutes.label"
                            :color="config.idleMinutes.color"
                            :thumb-label="true"
                        ></v-slider>
                    </div>
                </v-col>

                <v-col>
                    <v-switch
                        v-model="config.maintentanceCheck"
                        label="Maintenance Mode"
                    ></v-switch>
                </v-col>
            </v-row>
        </v-container>

        <v-card-text class="text-h5">Adjust App Color</v-card-text>
        <v-container>
            <v-row>
                <v-col>
                    <v-color-picker
                        dot-size="11"
                        hide-canvas
                        hide-inputs
                        hide-mode-switch
                        hide-sliders
                        mode="hexa"
                        show-swatches
                        swatches-max-height="80"
                        v-model="config.appcolor"
                    ></v-color-picker>
                </v-col>
            </v-row>
        </v-container>

        <v-spacer></v-spacer>
        <v-card-text text class="text-h5">Maps API settings</v-card-text>
        <v-container>
            <v-row>
                <v-col cols="4" md="6">
                    <v-select
                    v-model="config.mapProvider"
                    :items="mapProviders"
                    label="Map Provider"
                    >
                    </v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="4" md="3">
                    <v-text-field
                        v-model="config.osmKeys"
                        label="Open Street Maps API keys"
                        col="4"
                    ></v-text-field>
                </v-col>

                <v-col cols="15" md="3">
                    <v-text-field
                        v-model="config.googleMapKeys"
                        label="Google Maps API keys"
                        col="4"
                    ></v-text-field>
                </v-col>
            </v-row>
        </v-container>
        <v-btn text color="deep-purple accent-4" @click="saveConfig()"
            >Save to Config</v-btn
        >
    </div>
</template>

<script>
import apiRoutes from "../../../apiRoutes.js";
export default {
    mounted() {
        console.log("General Settings Sub component loaded");
    },
    data: () => ({
        mapProviders: ["Google Maps", "Open Street Maps", "MapBox"],
        config: {
            idleCheck: false,
            maintentanceCheck: false,
            appcolor: null,
            idleMinutes: { label: "Max Idle Time", val: 10, color: "blue" },
            googleMapKeys: "",
            osmKeys: "",
            mapProvider: ""
        }
    }),

    watch: {
        "config.appcolor": function() {
            console.log(this.config.appcolor.hexa);
        },
        "config.idleMinutes.val": function() {
            console.log(this.config.idleMinutes.val);
        }
    },

    methods: {
        // Arrow function to get the local vue instance
        saveConfig: async function() {
            const data = {
                idleCheck: this.config.idleCheck,
                maintentanceCheck: this.config.maintentanceCheck,
                appcolor: this.config.appcolor.hex,
                idleMinutes: this.config.idleMinutes,
                mapProvider: this.config.mapProvider,
                osmKeys: this.config.osmKeys,
                googleMapKeys: this.config.googleMapKeys
            };
            await apiRoutes.saveConfig(data);
        }
    }
};
</script>
