<script setup>
import OrderSummary from "@/components/OrderSummary.vue";
import CheckoutForm from "@/components/CheckoutForm.vue";
</script>

<template>
    <div
        class="relative min-h-screen bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center pt-28 pb-10 px-4 sm:px-6 lg:px-8"
    >
        <!-- Background Overlay -->
        <div class="absolute inset-0 bg-black/60 pointer-events-none"></div>

        <!-- Core Component Grids -->
        <div
            class="relative z-10 max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6"
        >
            <OrderSummary />
            <CheckoutForm />
        </div>
    </div>
</template>
