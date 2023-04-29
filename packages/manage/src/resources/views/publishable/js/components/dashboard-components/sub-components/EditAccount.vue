<template>
    <div>
        <v-card-text>
                    <p class="text-h4 text--primary">Edit Account</p>
                </v-card-text>
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-text-field
                        v-model="form.name"
                        :counter="255"
                        :rules="nameRules"
                        label="Name"
                        required
                    ></v-text-field>
                    <v-text-field
                        v-model="form.email"
                        label="Email"
                        required
                    ></v-text-field>
                    <v-text-field
                        v-model="form.password"
                        label="New Password"
                        :rules="min_pass"
                        type="password"
                        required
                    ></v-text-field>
                </v-form>
                <v-card-text v-if="statusMessage !== ''">
                    <div class="my-1">
                        {{ statusMessage }}
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-btn text color="deep-purple accent-4" v-on:click="save">
                        Save
                    </v-btn>

                    <v-btn text color="deep-red accent-4" v-on:click="save">
                        Disable Account
                    </v-btn>
                </v-card-actions>
                
    </div>
</template>


<script>
import apiRoutes from "../../../apiRoutes";
export default {
    data: () => ({
        valid: false,
        form: {
            name: "",
            password: "",
            email: "",
        },
        statusMessage: "",
        nameRules: [(v) => !!v || "name is required"],
        min_pass: [(v) => v.length > 4 || "Too short"],
    }),
    mounted() {
        this.initialize();
    },
    methods: {
        save: async function() {
            let data = {};
            for (let key in this.form) {
                if (this.form[key] !== "") {
                    data[key] = this.form[key];
                }
            }

            await apiRoutes.updateaccount(data);
        },
        initialize: async function() {
            const credentials = await apiRoutes.getaccount();
            this.form.name = credentials.name;
            this.form.email = credentials.email;
        },
    },
}
</script>