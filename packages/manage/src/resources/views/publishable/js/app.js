// Imports require
require("./bootstrap");
window.Vue = require("vue");
window.axios = require("axios");

// ESM Imports
import Vue from "vue";
import VueRouter from "vue-router";
import Vuetify from "vuetify";
import VueResource from "vue-resource";
import Vuex from "vuex";
import "leaflet/dist/leaflet.css";
import VueSocialauth from 'vue-social-auth';
import VueAxios from 'vue-axios'



// Vue level Cookie
import VueCookie from "vue-cookie";

import Dashboard from "./components/Dashboard";
import Login from "./components/Login";
import Register from "./components/Register";
import Home from "./components/dashboard-components/Home";
import UserList from "./components/dashboard-components/UserList";
import Account from "./components/dashboard-components/Account";
import Roles from "./components/dashboard-components/Roles";
import Organizations from "./components/dashboard-components/Organizations";
import Permissions from "./components/dashboard-components/Permissions";
import AppSettings from "./components/dashboard-components/AppSettings";

// For the default route
import NotFound from './components/NotFound';

// Vuex Implemntation
import store from "./store";


// Global Components
Vue.use(Vuetify);
Vue.use(VueCookie);
Vue.use(VueRouter);
// Vue.use(VueResource);
Vue.use(Vuex);
Vue.use(VueAxios, axios)
Vue.use(VueSocialauth,{
    providers: {
        facebook: {
            clientId: "687491615548327",
            redirectUri: "https://tesbench2.test/app/auth/facebook/callback",
        },
        google: {
            clientId: "230017145399-p2s0ohdn9raqsiurjvp3pd1coeqfses3.apps.googleusercontent.com",
            redirectUri: "https://tesbench2.test/app/auth/google/callback",
        },
        github:{
            clientId: "71b5b601d038855e9e42",
            redirectUri: "https://tesbench2.test/app/auth/github/callback",
        }
    },
})

const files = require.context("./", true, /\.vue$/i);
files.keys().map((key) =>
    Vue.component(
        key
            .split("/")
            .pop()
            .split(".")[0],
        files(key).default
    )
);


const routes = [
    {
        path: "/app/dashboard",
        component: Home,
        name: "Home",
        beforeEnter: store.routeGuard,
    },
    {
        path: "/app/users",
        component: UserList,
        name: "Users",
        beforeEnter: store.routeGuard,
    },
    {
        path: "/app/roles",
        component: Roles,
        name: "Roles",
        beforeEnter: store.routeGuard,
    },
    {
        path: "/app/organizations",
        component: Organizations,
        name: "Organizations",
        beforeEnter: store.routeGuard,
    },
    {
        path: "/app/permissions",
        component: Permissions,
        name: "Permissions",
        beforeEnter: store.routeGuard,
    },
    {
        path: "/app/account",
        component: Account,
        name: "Account",
        beforeEnter: store.routeGuard,
    },
    {
        path: "/app/login",
        component: Login,
        name: "Login",
        beforeEnter: store.routeGuard,
    },
    {
        path: "/app/register",
        component: Register,
        name: "Register",
    },
    {
        path: "/app/app-settings",
        component: AppSettings,
        name: "App Settings",
        beforeEnter: store.routeGuard
    },
    {
        path: "/app/auth/:provider/callback",
        component: {
            template: '<div class="auth-component"></div>'
        }
    },
    {
        path: "*",
        component: NotFound,
        name: "404"
    }
];



const router = new VueRouter({
    mode: "history",
    routes,
});

const app = new Vue({
    router,
    vuetify: new Vuetify(),
    render: (h) => h(Dashboard),
}).$mount("#app");
