<script setup>
import { computed } from "vue";
import { useOrderStore } from "@/stores/order";
import { useAuthStore } from "@/stores/auth";
import { useRouter, useRoute } from "vue-router";

const orderStore = useOrderStore();
const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const latestActiveOrder = computed(() => {
    return orderStore.activeUserOrders[0] || null;
});

// Hide widget if admin, not logged in, or already on track-order page
const isVisible = computed(() => {
    return latestActiveOrder.value !== null && route.name !== "track-order";
});

const goToTrackOrder = () => {
    router.push({ name: "track-order" });
};
</script>

<template>
    <transition name="slide-up">
        <div
            v-if="isVisible"
            @click="goToTrackOrder"
            class="fixed bottom-2 sm:bottom-5 w-[calc(100%-0.75rem)] sm:w-72 max-w-xs left-1/2 -translate-x-1/2 z-100 bg-black/60 backdrop-blur-md border border-white/20 rounded-lg p-1.5 sm:p-3 cursor-pointer active:scale-95 sm:hover:scale-[1.02] transition-all duration-300 flex items-center gap-2 sm:gap-3"
        >
            <!-- Icon -->
            <div
                class="relative w-10 h-10 flex items-center justify-center bg-white/10 rounded-full shrink-0"
            >
                <i
                    v-if="latestActiveOrder.status === 'pending'"
                    class="pi pi-clock text-yellow-400 text-lg animate-pulse"
                ></i>

                <i
                    v-else-if="latestActiveOrder.status === 'preparing'"
                    class="pi pi-spin pi-spinner text-blue-400 text-lg"
                ></i>

                <i
                    v-else-if="latestActiveOrder.status === 'out_for_delivery'"
                    class="pi pi-car text-purple-500 text-lg animate-pulse"
                ></i>

                <i
                    v-else-if="latestActiveOrder.status === 'ready_for_pickup'"
                    class="pi pi-shopping-bag text-orange-500 text-lg animate-pulse"
                ></i>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <p
                    class="text-[10px] text-gray-400 font-medium uppercase tracking-wide"
                >
                    Order #{{ latestActiveOrder.id }}
                </p>
                <h3
                    class="font-semibold text-white text-sm capitalize truncate"
                >
                    {{
                        latestActiveOrder.status
                            .replace(/_/g, " ")
                            .replace(/\b\w/g, (l) => l.toUpperCase())
                    }}...
                </h3>
            </div>

            <!-- Arrow -->
            <div class="shrink-0 text-white bg-white/20 rounded-full p-1">
                <i class="pi pi-angle-right text-sm"></i>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: translate(-50%, 120%);
}
</style>
