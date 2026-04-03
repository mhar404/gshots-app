<script setup>
import { computed, onMounted } from "vue";
import { useOrderStore } from "@/stores/order";
import { useAuthStore } from "@/stores/auth";

const orderStore = useOrderStore();
const authStore = useAuthStore();

// Filter to only show completed or cancelled orders for the current user
const pastOrders = computed(() => {
    return orderStore.orders.filter(
        (o) =>
            o.user_id === authStore.user?.id &&
            (o.status === "completed" || o.status === "cancelled"),
    );
});

onMounted(() => {
    if (orderStore.orders.length === 0) {
        orderStore.fetchOrders();
    }
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
    });
};
</script>

<template>
    <div
        class="relative min-h-screen bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center pt-24 pb-10 px-4 sm:px-6 lg:px-8 text-white flex flex-col items-center"
    >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 w-full max-w-4xl">
            <div class="mb-6 text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-2">
                    <span
                        class="bg-linear-to-r from-red-500 via-orange-500 to-yellow-500 bg-clip-text text-transparent"
                    >
                        Order History
                    </span>
                </h1>
                <p class="text-sm text-gray-400">
                    Total orders: {{ pastOrders.length }}
                </p>
            </div>

            <!-- Loading State -->
            <div
                v-if="orderStore.loading"
                class="flex justify-center items-center py-20"
            >
                <i class="pi pi-spin pi-spinner text-4xl text-red-500"></i>
            </div>

            <!-- Empty State -->
            <div
                v-else-if="pastOrders.length === 0"
                class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-10 text-center shadow-lg"
            >
                <i class="pi pi-receipt text-6xl text-gray-500 mb-4 block"></i>
                <h2 class="text-xl font-semibold mb-2">No past orders yet</h2>
                <p class="text-gray-400">
                    Once your order is complete or cancelled, it will appear
                    here.
                </p>
                <RouterLink
                    to="/menu"
                    class="mt-6 inline-block bg-red-600 hover:bg-red-700 px-6 py-3 rounded-xl font-medium transition"
                >
                    Browse Menu
                </RouterLink>
            </div>

            <!-- List State -->
            <div v-else class="space-y-6">
                <div
                    v-for="order in pastOrders"
                    :key="order.id"
                    class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 sm:p-6 rounded-2xl shadow-lg transition-transform hover:scale-[1.01]"
                >
                    <!-- Header -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-white/10 pb-4 mb-4"
                    >
                        <div>
                            <p
                                class="text-sm tracking-wider text-gray-400 font-medium font-mono"
                            >
                                ORDER #{{ order.id }}
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                        <div class="mt-3 sm:mt-0 flex gap-3 items-center">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-green-500/20 text-green-400 border border-green-500/30':
                                        order.status === 'completed',
                                    'bg-red-500/20 text-red-400 border border-red-500/30':
                                        order.status === 'cancelled',
                                }"
                            >
                                {{ order.status }}
                            </span>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Side: Items -->
                        <div>
                            <p
                                class="text-xs text-gray-500 uppercase tracking-wide mb-2"
                            >
                                Items Ordered
                            </p>
                            <div class="space-y-2">
                                <div
                                    v-for="(item, i) in order.items"
                                    :key="i"
                                    class="flex justify-between items-center text-sm"
                                >
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 font-mono"
                                            >x{{ item.quantity }}</span
                                        >
                                        <span
                                            class="font-medium text-gray-200"
                                            >{{ item.name }}</span
                                        >
                                    </div>
                                    <span class="text-gray-400">
                                        ₱{{
                                            (
                                                item.price * item.quantity
                                            ).toFixed(2)
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Details -->
                        <div
                            class="bg-black/20 rounded-xl p-4 border border-white/5"
                        >
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-500 uppercase text-xs"
                                    >Type</span
                                >
                                <span class="capitalize text-gray-200">{{
                                    order.order_type
                                }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-500 uppercase text-xs"
                                    >Payment</span
                                >
                                <span class="capitalize text-gray-200">{{
                                    order.payment_method
                                }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center mt-4 pt-4 border-t border-white/10"
                            >
                                <span class="text-gray-400">Total</span>
                                <span class="text-xl font-bold text-red-400"
                                    >₱{{ order.total }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
