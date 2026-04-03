<script setup>
import { useUiStore } from "@/stores/ui";

const ui = useUiStore();
</script>

<template>
    <transition name="fade-slide">
        <div
            v-if="ui.isCartToastOpen"
            class="fixed top-20 right-5 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50"
        >
            <i class="pi pi-check-circle text-lg"></i>
            <span>{{ ui.cartToastMessage }}</span>
        </div>
    </transition>
</template>

<style>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.3s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
