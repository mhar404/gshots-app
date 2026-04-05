<script setup>
import { onMounted, computed, watch } from "vue";
import { useOrderStore } from "../stores/order";
import { useAuthStore } from "../stores/auth";
import { useRouter } from "vue-router";

const router = useRouter();
const orderStore = useOrderStore();
const authStore = useAuthStore();

const orderStatus = computed(() =>
    orderStore.orders.filter(
        (o) =>
            o.user_id === authStore.user.id &&
            o.status !== undefined &&
            (o.status === "pending" ||
                o.status === "preparing" ||
                o.status === "out_for_delivery" ||
                o.status === "ready_for_pickup"),
    ),
);

watch(
    () => orderStatus.value.length,
    (length) => {
        if (length === 0) {
            router.push({ name: "order-history" });
        }
    },
    { deep: true },
);

onMounted(async () => {
    await orderStore.fetchOrders();
});
</script>

<template>
    <div
        class="relative min-h-screen bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center pt-28 pb-10 px-4 sm:px-6 lg:px-8 text-white"
    >
        <div class="absolute inset-0 bg-black/70"></div>

        <div
            v-for="order in orderStatus"
            :key="order.id"
            class="relative z-10 max-w-xl mx-auto space-y-5 mb-5"
        >
            <div
                class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 rounded-2xl shadow space-y-4"
            >
                <div>
                    <p class="text-xs text-gray-400">Order ID</p>
                    <h1 class="text-lg font-semibold">{{ order.id }}</h1>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <h2 class="text-xl font-bold">
                        {{
                            order.status
                                .replace(/_/g, " ")
                                .replace(/\b\w/g, (l) => l.toUpperCase())
                        }}
                    </h2>
                </div>

                <div class="mt-3">
                    <div
                        class="flex justify-between text-xs mb-2 text-gray-400"
                    >
                        <span>Pending</span>
                        <span>Preparing</span>
                        <span>
                            {{
                                order.order_type === "delivery"
                                    ? "Out for delivery"
                                    : order.order_type === "pickup"
                                      ? "Ready for pickup"
                                      : order.status
                            }}
                        </span>
                    </div>

                    <div class="w-full h-2 bg-black/40 rounded overflow-hidden">
                        <div
                            class="h-2 rounded transition-all duration-500"
                            :class="{
                                'bg-yellow-500': order.status === 'pending',
                                'bg-blue-500': order.status === 'preparing',
                                'bg-purple-500':
                                    order.status === 'out_for_delivery',
                                'bg-orange-500':
                                    order.status === 'ready_for_pickup',
                            }"
                            :style="{
                                width:
                                    order.status === 'pending'
                                        ? '25%'
                                        : order.status === 'preparing'
                                          ? '50%'
                                          : order.status === 'out_for_delivery'
                                            ? '100%'
                                            : order.status ===
                                                'ready_for_pickup'
                                              ? '100%'
                                              : '0%',
                            }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- 🧾 CARD 2: FULL DETAILS -->
            <div
                class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 rounded-2xl shadow space-y-5"
            >
                <!-- Customer -->
                <div>
                    <p class="text-xs text-gray-400">Customer</p>
                    <p class="font-medium">{{ order.name }}</p>
                    <p class="text-xs text-gray-400">{{ order.phone }}</p>
                </div>

                <!-- Delivery -->
                <div>
                    <p class="text-xs text-gray-400">Order Type</p>
                    <p class="capitalize">{{ order.order_type }}</p>
                    <p class="text-xs text-gray-400">{{ order.address }}</p>
                </div>

                <!-- Items -->
                <div>
                    <p class="text-xs text-gray-400">Items</p>

                    <div
                        v-for="(item, i) in order.items"
                        :key="i"
                        class="flex justify-between items-center py-2 border-b border-white/5 last:border-none"
                    >
                        <div>
                            <p class="text-sm">{{ item.name }}</p>
                            <p class="text-xs text-gray-400">
                                x{{ item.quantity }}
                            </p>
                        </div>

                        <p class="text-sm font-medium">
                            ₱{{ item.price * item.quantity }}
                        </p>
                    </div>
                </div>

                <!-- Payment + Total -->
                <div
                    class="flex justify-between items-center pt-3 border-t border-white/10"
                >
                    <div>
                        <p class="text-xs text-gray-400">Payment</p>
                        <p class="capitalize text-sm">
                            {{ order.payment_method }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-gray-400">Total</p>
                        <p class="text-red-500 font-bold text-lg">
                            ₱{{ order.total }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
