// Symbolic link test for Deployment
<template>
    <div>
        <v-app-bar color="deep-purple accent-4" dark prominent>
            <v-app-bar-nav-icon
                @click.stop="drawer = !drawer"
            ></v-app-bar-nav-icon>
            <!-- Abide SRP -->
            <v-toolbar-title class="justify-center">{{
                currentRouteName
            }}</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn icon>
                <v-menu bottom left>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn dark icon v-bind="attrs" v-on="on">
                            <v-icon>mdi-dots-vertical</v-icon>
                        </v-btn>
                    </template>

                    <v-list>
                        <v-list-item v-for="(item, i) in menu_items" :key="i">
                            <v-list-item-title v-on:click="handlers.logout">{{
                                item.title
                            }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-btn>
        </v-app-bar>

        <v-navigation-drawer v-model="drawer" absolute bottom temporary>
            <v-list>
                <v-list-item
                    active-class="deep-purple--text text--accent-4"
                    v-model="group"
                    v-for="item in items"
                    :key="item.title"
                    link
                    @click="$router.push({ path: item.link })"
                >
                    <v-list-item-icon>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
    </div>
</template>

<script>
import apiRoutes from "../../apiRoutes";
import Swal from "sweetalert2/src/sweetalert2.js";
export default {
    data() {
        return {
            timer: 5, // minutes
            interval: 1000,
            timeoutHandle: null, // Accepts function
            timerInterval: null,
            items: [
                {
                    title: "Dashboard",
                    icon: "mdi-view-dashboard",
                    link: "/app/dashboard"
                },
                { title: "Users", icon: "mdi-account-box", link: "/app/users" },
                {
                    title: "Roles",
                    icon: "mdi-account-check",
                    link: "/app/roles"
                },
                {
                    title: "Organizations",
                    icon: "mdi-handshake-outline",
                    link: "/app/organizations"
                },
                {
                    title: "Permissions",
                    icon: "mdi-shield-check",
                    link: "/app/permissions"
                },
                {
                    title: "Account Settings",
                    icon: "mdi-gavel",
                    link: "/app/account"
                },
                {
                    title: "App Settings",
                    icon: "mdi-application-cog",
                    link: "/app/app-settings"
                }
            ],
            menu_items: [{ title: "Logout", action: "handlers.logout" }],
            drawer: false,
            group: null,
            handlers: {
                logout: this.logout
            }
        };
    },
    created() {
        console.log("Nav component mounted");
        // Interval for idle-detection
        // this.remind(()=> {
        //         Swal.fire({
        //             title: "Idle detection",
        //             html:
        //                 "Are you stil active? </br> You will be logged out in <b></b>",
        //             timer: 5000,
        //             timerProgressBar: true,
        //             showCancelButton: true,
        //             cancelButtonText: "I am still active",
        //             didOpen: () => {
        //                 const template = Swal.getHtmlContainer().querySelector(
        //                     "b"
        //                 );
        //                 this.timerInterval = window.setInterval(()=>{
        //                     template.textContent = Math.floor(
        //                         Swal.getTimerLeft() / 1000
        //                     );
        //                 }, 100);
        //             }
        //         }).then(async res => {
        //             if (res.dismiss === Swal.DismissReason.timer) {
        //                 await apiRoutes.logout();
        //                 window.location.href = "/app/login?reason=kicked'";
        //             } else if (res.dismiss === Swal.DismissReason.cancel) {
        //                 window.clearInterval(this.timerInterval);
        //                 this.remind();
        //             }
        //         });
        //     }, this.interval * this.timer)
    },

    watch: {
        group() {
            this.drawer = false;
        }
    },
    props: ["title"],
    computed: {
        currentRouteName: function() {
            return this.$route.name;
        }
    },
    methods: {
        logout: async function() {
            const res = await apiRoutes.logout();
            console.log(res);
            window.location.href = "/app/login";
        },

        remind: function(callback, milliseconds) {
            let wait = window.setTimeout(callback, milliseconds);

            document.addEventListener("mousemove", () => {
                window.clearTimeout(wait);
                wait = window.setTimeout(callback, milliseconds);
            });
        }
    }
};
</script>
