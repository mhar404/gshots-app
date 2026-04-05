<script setup>
import Loader from "./components/Loader.vue";
import { RouterView, useRoute } from "vue-router";
import { ref, onMounted, computed } from "vue";
import TopNav from "@/components/TopNav.vue";

// Impor modalities
import AuthModal from "@/components/AuthModal.vue";
import CartModal from "@/components/CartModal.vue";
import CartToast from "@/components/CartToast.vue";
import AuthToast from "@/components/AuthToast.vue";
import ActiveOrderWidget from "@/components/ActiveOrderWidget.vue";
import ChatBot from "@/components/ChatBot.vue";
import { useOrderStore } from "@/stores/order";

const loading = ref(true);
const route = useRoute();
const orderStore = useOrderStore();

onMounted(() => {
    setTimeout(() => {
        loading.value = false;
    }, 1000);

    // Global stable websocket connection
    if (window.Echo) {
        const ordersChannel = window.Echo.channel("orders");

        ordersChannel.listen(".order.created", (e) => {
            console.log("New Order (Global):", e.order);
            orderStore.addOrderRealtime(e.order);
        });

        ordersChannel.listen(".order.updated", (e) => {
            console.log("Updated Order (Global):", e.order);
            orderStore.updateOrderRealtime(e.order);
        });
    }
});

const isAdminRoute = computed(() => {
    return route.path.startsWith("/admin");
});
</script>

<template>
    <Loader v-if="loading" />

    <!-- Public Header / Modals -->
    <template v-if="!isAdminRoute">
        <TopNav />
        <AuthModal />
        <CartModal />
        <CartToast />
        <AuthToast />
        <ActiveOrderWidget />
        <ChatBot />
    </template>


    <main :class="{ 'h-screen overflow-hidden': isAdminRoute }">
        <RouterView />
    </main>
</template>
