<template>
    <v-app>
        <v-main>
            <v-card class="mx-auto pa-4 ma-12" max-width="480" outlined>
                <v-card-text>
                    <p class="text-h4 text--primary">Login</p>
                </v-card-text>

                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-text-field
                        v-model="email"
                        label="Email"
                        required
                    ></v-text-field>
                    <v-text-field
                        v-model="password"
                        label="Password"
                        type="password"
                        required
                    ></v-text-field>
                </v-form>

                <v-card-actions>
                    <v-btn text color="deep-purple accent-4" @click="login">
                        Login
                    </v-btn>
                    <v-btn
                        text
                        color="deep-purple accent-4"
                        href="/app/register"
                    >
                        Register
                    </v-btn>
                </v-card-actions>

                <v-spacer></v-spacer>
                <v-card-text>
                    <p class="text--primary">OR Login with</p>
                </v-card-text>
                <v-container>
                    <v-row align="center">
                        <v-col cols="2" md="4">
                            <v-card-text v-on:click="AuthProvider('facebook')"> <v-icon>mdi-facebook</v-icon> Facebook </v-card-text>
                        </v-col>
                        <v-col cols="2" md="4">
                            <v-card-text v-on:click="AuthProvider('google')"><v-icon>mdi-google</v-icon> Google</v-card-text>
                        </v-col>
                        <v-col cols="2" md="4">
                            <v-card-text v-on:click="AuthProvider('github')"><v-icon>mdi-github</v-icon> Github</v-card-text>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card>
        </v-main>
    </v-app>
</template>

<script>
import apiRoutes from "../apiRoutes";
export default {
    mounted() {
        console.log("Component mounted.");
    },

    data: () => ({
        valid: false,
        email: "",
        password: ""
    }),
    methods: {
        login: async function(e) {
            const response = await apiRoutes.login(this.email, this.password);
            this.$cookie.set("auth_token", `${response.token}`);
            window.location.href = "/app/dashboard";
        },

        AuthProvider(provider) {
              const self = this
              this.$auth.authenticate(provider).then(response =>{
                self.SocialLogin(provider,response)
                }).catch(err => {
                    console.log({err:err})
                })
            },

        SocialLogin: async function (provider, response){
            const socialRes = await apiRoutes.socialAuth(provider, response)
            this.$cookie.set("auth_token", `${socialRes.token}`);
            window.location.href = "/app/dashboard";
        }
    }
};
</script>
