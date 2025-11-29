import "./assets/styles.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import CountryFlag from "vue-country-flag-next";

import App from "./App.vue";
import router from "./router";
import { VueQueryPlugin } from "@tanstack/vue-query";
import axios from "axios";

const app = createApp(App);

// Headers required for Laravel to not blow up
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.withCredentials = true;
axios.defaults.xsrfCookieName = "XSRF-TOKEN";
axios.defaults.xsrfHeaderName = "X-XSRF-TOKEN";
axios.defaults.withXSRFToken = true;
axios.defaults.headers.common["Content-Type"] = "application/json";

app.use(createPinia());
app.use(router);
app.use(VueQueryPlugin);
app.component("CountryFlag", CountryFlag);

app.mount("#app");
