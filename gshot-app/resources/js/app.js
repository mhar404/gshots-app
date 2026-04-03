import "./bootstrap";
import "../css/app.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { createPinia } from "pinia";
import { useAuthStore } from "@/stores/auth";
import VueApexCharts from "vue3-apexcharts";

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.component("apexchart", VueApexCharts);

const authStore = useAuthStore();
authStore.restoreUser();

app.mount("#app");
