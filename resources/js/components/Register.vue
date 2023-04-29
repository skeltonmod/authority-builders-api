<template>
    <v-app>
        <v-main>
            <v-card class="mx-auto pa-4 ma-12" max-width="520" outlined>
                <v-card-text>
                    <p class="text-h4 text--primary">
                        Register <span v-if="invited_flag">Invited</span> User
                    </p>
                </v-card-text>

                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-text-field
                        v-model="name"
                        :counter="255"
                        :rules="nameRules"
                        label="Name"
                        required
                    ></v-text-field>
                    <v-text-field
                        v-model="email"
                        label="Email"
                        required
                    ></v-text-field>
                    <v-text-field
                        v-model="password"
                        label="Password"
                        :rules="min_pass"
                        type="password"
                        required
                    ></v-text-field>
                    <v-text-field
                        v-model="confirm_password"
                        label="Confirm Password"
                        :rules="[password_rules]"
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
                    <v-btn
                        text
                        color="deep-purple accent-4"
                        v-on:click="register"
                    >
                        Register
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-main>
    </v-app>
</template>

<script>
import apiRoutes from "../apiRoutes";
export default {
    mounted() {
        console.log("Register Component Loaded");
    },
    // Hackerman validation form
    data: () => ({
        valid: false,
        invited_flag: false,
        name: "",
        password: "",
        confirm_password: "",
        email: "",
        statusMessage: "",
        nameRules: [v => !!v || "name is required"],
        min_pass: [v => v.length > 4 || "Too short"]
    }),
    computed: {
        password_rules: function() {
            return () =>
                this.password === this.confirm_password ||
                "Password's do not match";
        }
    },

    mounted() {
        this.checkInvite();
    },

    methods: {
        register: async function(e) {
            this.statusMessage = "Please Wait...";
            const response = await apiRoutes.register(
                this.email,
                this.password,
                this.name,
                this.invited_flag
            );
            this.statusMessage = response.message;
        },

        checkInvite: function() {
            let url = new URL(window.location.href);
            let params = new URLSearchParams(url.search);

            if (params.has("invited_user") && params.has("token")) {
                const invitee_email = decodeURIComponent(
                    params.get("invited_user")
                );
                const token = decodeURIComponent(params.get("token"));

                if (!bcrypt.compareSync(invitee_email, token)) {
                    this.$router.push({ name: "404" });
                }else{
                    this.invited_flag = true;
                    this.email = invitee_email;
                }
            }
        }
    }
};
</script>
