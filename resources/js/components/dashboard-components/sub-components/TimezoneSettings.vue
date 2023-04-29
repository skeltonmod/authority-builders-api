<template>
    <div>
        <v-container class="d-flex">
            <v-row class="justify-center">
                    <v-col cols="12" md="6">
                        <v-card-text class="text-h5">Account Timezone</v-card-text>
                    </v-col>
                </v-row>
        </v-container>
        <v-container class="d-flex">
            <v-row class="justify-center">
                    <v-col cols="12" md="6">
                        <v-select
                            :items="timezones"
                            label="Timezone"
                            solo
                        ></v-select>
                    </v-col>
                </v-row>
        </v-container>

        
    </div>
</template>

<script>
import apiRoutes from "../../../apiRoutes.js";
export default {
    mounted() {
        this.setupTimezone();
    },
    data: () => ({
        timezones: []
    }),
    methods: {
        setupTimezone: async function() {
            const response = await apiRoutes.getTimezones();

            for (let key in response) {
                this.timezones.push(response[key]);
            }
        }
    }
};
</script>
