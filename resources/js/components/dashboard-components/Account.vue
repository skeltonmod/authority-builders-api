<template>
    <div>
        <v-app
            id="inspire"
            class="mx-auto overflow-hidden rounded-0"
            height="100%"
            width="100%"
        >
            <Nav />
            <!-- TABBED Settings -->
            <v-card class="mx-auto pa-4 ma-12" width="85%" outlined>
                <v-tabs
                    background-color="deep-purple accent-4"
                    centered
                    dark
                    icons-and-text
                >
                    <v-tabs-slider></v-tabs-slider>

                    <v-tab v-for="(item, index) in tabs" 
                    v-bind:key="index"
                    @click="tab = index"
                    >
                        {{item.name}}
                        <v-icon>{{item.icon}}</v-icon>
                    </v-tab>
                </v-tabs>
                <v-tabs-items>
                    <keep-alive>
                        <component v-bind:is="currentTabComponent.component"></component>
                    </keep-alive>
                </v-tabs-items>
            </v-card>
        </v-app>
    </div>
</template>

<script>
import Nav from "./Nav.vue";
import EditAccount from "./sub-components/EditAccount.vue";
import Maps from "./sub-components/Maps.vue";
export default {
    data: () => ({
        tabs: [
            {
                name: "Edit Account",
                component: EditAccount,
                icon: "mdi-account-box"
            },
            {
                name: "Maps",
                component: Maps,
                icon: "mdi-routes"
            }
        ],
        tab: 0
    }),
    components: { Nav },
    computed: {
        currentTabComponent: function() {
            return this.tabs[this.tab]
        },
    },
};
</script>
