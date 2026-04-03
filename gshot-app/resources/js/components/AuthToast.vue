<script setup>
import { useUiStore } from "@/stores/ui";

const ui = useUiStore();
</script>

<template>
    <transition name="toast">
        <div
            v-if="ui.isAuthToastOpen"
            class="fixed top-20 right-5 px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 z-50 text-white"
            :class="{
                'bg-green-600': ui.authToastType === 'login',
                'bg-blue-600': ui.authToastType === 'register',
                'bg-gray-700': ui.authToastType === 'logout',
            }"
        >
            <!-- ICON -->
            <i
                class="pi text-lg"
                :class="{
                    'pi-sign-in': ui.authToastType === 'login',
                    'pi-user-plus': ui.authToastType === 'register',
                    'pi-sign-out': ui.authToastType === 'logout',
                }"
            ></i>

            <!-- MESSAGE -->
            <span>{{ ui.authToastMessage }}</span>
        </div>
    </transition>
</template>

<style>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
