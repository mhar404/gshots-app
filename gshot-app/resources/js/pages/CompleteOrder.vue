<script setup>
import { onMounted, computed } from "vue";
import { useOrderStore } from "@/stores/order";

const orderStore = useOrderStore();

onMounted(() => {
    orderStore.fetchOrders();
});

const completedOrders = computed(() =>
    orderStore.orders.filter((o) => o.status === "completed"),
);

const totalCompleted = computed(() => completedOrders.value.length);

const formatDate = (dateString) => {
    if (!dateString) return "";
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
        class="relative min-h-screen bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center pt-10 pb-10 px-4 sm:px-6 lg:px-8 text-white flex flex-col items-center"
    >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 w-full max-w-4xl">
            <!-- Header -->
            <div class="mb-8 text-center space-y-3">
                <!-- Status badge -->
                <div class="flex items-center justify-center gap-2">
                    <span
                        class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"
                    ></span>
                    <span
                        class="text-xs uppercase tracking-wider text-white/60"
                    >
                        History
                    </span>
                </div>

                <!-- Title -->
                <h1
                    class="text-4xl font-bold leading-tight bg-linear-to-r from-red-500 via-orange-500 to-yellow-500 bg-clip-text text-transparent"
                >
                    Completed Orders
                </h1>

                <!-- Subtitle -->
                <p class="text-sm text-white/70">
                    Total completed:
                    <span class="text-white font-semibold">{{
                        totalCompleted
                    }}</span>
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
                v-else-if="completedOrders.length === 0"
                class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-10 text-center shadow-lg"
            >
                <i
                    class="pi pi-check-circle text-6xl text-gray-500 mb-4 block"
                ></i>
                <h2 class="text-xl font-semibold mb-2">
                    No completed orders yet
                </h2>
                <p class="text-gray-400">
                    Once orders are completed, they will appear here.
                </p>
            </div>

            <!-- List State -->
            <div v-else class="space-y-6">
                <div
                    v-for="order in completedOrders"
                    :key="order.id"
                    class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 sm:p-6 rounded-2xl shadow-lg transition-transform hover:scale-[1.01]"
                >
                    <!-- Card Header -->
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
                                class="bg-green-500/20 text-green-400 border border-green-500/30 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                            >
                                Completed
                            </span>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div
                        class="mb-4 bg-black/20 rounded-xl p-4 border border-white/5 flex flex-col md:flex-row justify-between gap-4"
                    >
                        <div>
                            <p
                                class="text-xs text-gray-500 uppercase tracking-wide mb-1"
                            >
                                Customer
                            </p>
                            <p class="text-gray-200 font-medium">
                                {{ order.name }}
                            </p>
                            <p class="text-sm text-gray-400">
                                {{ order.phone }}
                            </p>
                        </div>
                        <div v-if="order.address" class="md:text-right">
                            <p
                                class="text-xs text-gray-500 uppercase tracking-wide mb-1"
                            >
                                Address
                            </p>
                            <p
                                class="text-sm text-gray-400 max-w-[200px] md:ms-auto"
                            >
                                {{ order.address }}
                            </p>
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
