<script setup>
import { onMounted, ref, computed } from "vue";
import { useOrderStore } from "@/stores/order";
import { useRouter } from "vue-router";

const router = useRouter();
const orderStore = useOrderStore();

const selectedOrder = ref(null);

const goToCompletedOrders = () => {
    router.push("/admin/complete-orders");
};

onMounted(() => {
    orderStore.fetchOrders();
});

// COMPUTED
const totalOrders = computed(() => orderStore.orders.length);

const pendingOrders = computed(
    () => orderStore.orders.filter((o) => o.status === "pending").length,
);

const completedOrders = computed(
    () => orderStore.orders.filter((o) => o.status === "completed").length,
);

const activeOrders = computed(() =>
    orderStore.orders.filter((o) => o.status !== "completed"),
);

// ACTIONS
const selectOrder = (order) => {
    selectedOrder.value = order;
};

const updateStatus = async (status) => {
    if (!selectedOrder.value) return;

    await orderStore.updateOrderStatus(selectedOrder.value.id, status);

    // sync from store (NOT manual)
    const updated = orderStore.orders.find(
        (o) => o.id === selectedOrder.value.id,
    );

    if (updated) {
        selectedOrder.value = updated;
    }

    if (status === "completed") {
        selectedOrder.value = null;
    }
};
</script>

<template>
    <div
        class="relative min-h-screen bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center text-white py-10 px-5 md:px-15"
    >
        <div class="absolute inset-0 bg-black/70"></div>

        <div class="relative z-10 space-y-6">
            <div class="space-y-3">
                <!-- Status badge -->
                <div class="flex items-center gap-2">
                    <span
                        class="w-2 h-2 rounded-full bg-green-500 animate-pulse"
                    ></span>
                    <span
                        class="text-xs uppercase tracking-wider text-white/60"
                    >
                        Live Orders
                    </span>
                </div>

                <!-- Title -->
                <h1
                    class="text-4xl font-bold leading-tight bg-linear-to-r from-red-500 via-yellow-400 to-orange-500 bg-clip-text text-transparent"
                >
                    Active Orders
                </h1>

                <!-- Subtitle -->
                <p class="text-sm text-white/70 max-w-md leading-relaxed">
                    Monitor and manage all ongoing orders in real time with live
                    updates and status tracking.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                <div class="bg-white/10 border border-white/10 rounded-xl p-4">
                    <p class="text-xs text-gray-400">Total Orders</p>
                    <h2 class="text-2xl font-semibold">{{ totalOrders }}</h2>
                </div>

                <div class="bg-white/10 border border-white/10 rounded-xl p-4">
                    <p class="text-xs text-gray-400">Pending</p>
                    <h2 class="text-2xl font-semibold text-yellow-400">
                        {{ pendingOrders }}
                    </h2>
                </div>

                <div
                    @click="goToCompletedOrders"
                    class="group relative bg-white/10 border border-white/10 rounded-xl p-4 cursor-pointer transition-all duration-300 hover:bg-white/20 hover:scale-[1.02] hover:border-green-400/40"
                >
                    <div
                        class="absolute top-3 right-3 text-gray-400 group-hover:text-green-400 transition"
                    >
                        →
                    </div>

                    <p
                        class="text-xs text-gray-400 group-hover:text-green-300 transition"
                    >
                        Completed Orders
                    </p>

                    <h2 class="text-2xl font-semibold text-green-400">
                        {{ completedOrders }}
                    </h2>

                    <!-- hint text -->
                    <p
                        class="text-[11px] text-gray-500 opacity-70 group-hover:opacity-100 transition"
                    >
                        Click to view all completed orders
                    </p>
                </div>
            </div>

            <div
                class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-4"
            >
                <!-- LEFT PANEL -->
                <div
                    class="lg:col-span-4 bg-white/10 border border-white/10 rounded-xl flex flex-col max-h-[70vh]"
                >
                    <div class="p-5 border-b border-white/10">
                        <h1 class="font-semibold">Orders</h1>
                        <p class="text-xs text-gray-400">Tap to view details</p>
                    </div>

                    <div class="flex-1 overflow-y-auto p-3 space-y-2">
                        <div
                            v-for="order in activeOrders"
                            :key="order.id"
                            @click="selectOrder(order)"
                            class="p-3 rounded-lg cursor-pointer transition hover:bg-white/10 border"
                            :class="
                                selectedOrder?.id === order.id
                                    ? 'border-red-500 bg-white/5'
                                    : 'border-white/10'
                            "
                        >
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold"
                                    >#{{ order.id }}</span
                                >
                                <span class="text-xs text-gray-400"
                                    >₱{{ order.total }}</span
                                >
                            </div>

                            <p class="text-sm text-gray-300 mt-1 truncate">
                                {{ order.name }}
                            </p>

                            <span
                                class="text-[11px] px-2 py-0.5 rounded-full mt-2 inline-block"
                                :class="{
                                    'bg-yellow-500/20 text-yellow-300':
                                        order.status === 'pending',
                                    'bg-blue-500/20 text-blue-300':
                                        order.status === 'preparing',
                                    'bg-purple-500/20 text-purple-300':
                                        order.status === 'out_for_delivery',
                                    'bg-orange-500/20 text-orange-300':
                                        order.status === 'ready_for_pickup',
                                }"
                            >
                                {{
                                    order.status
                                        .replace(/_/g, " ")
                                        .replace(/\b\w/g, (l) =>
                                            l.toUpperCase(),
                                        ) || "pending"
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT PANEL -->
                <div
                    class="lg:col-span-8 bg-white/10 border border-white/10 rounded-xl flex flex-col"
                >
                    <div class="p-5 border-b border-white/10">
                        <h2 class="font-semibold">
                            {{
                                selectedOrder
                                    ? `Order #${selectedOrder.id}`
                                    : "Order Details"
                            }}
                        </h2>
                        <p class="text-xs text-gray-400">
                            Manage order information
                        </p>
                    </div>

                    <div class="flex-1 overflow-y-auto p-5">
                        <!-- SHOW EMPTY STATE -->
                        <div
                            v-if="
                                !selectedOrder ||
                                selectedOrder.status === 'completed'
                            "
                            class="h-full flex items-center justify-center text-gray-500"
                        >
                            Select an order
                        </div>

                        <!-- SHOW ORDER DETAILS -->
                        <div v-else class="space-y-4 max-w-2xl">
                            <!-- CUSTOMER -->
                            <div
                                class="bg-black/20 border border-white/10 rounded-xl p-4"
                            >
                                <h3 class="text-sm text-gray-400 mb-2">
                                    Customer
                                </h3>
                                <p class="font-semibold">
                                    {{ selectedOrder.name }}
                                </p>
                                <p class="text-sm text-gray-400">
                                    {{ selectedOrder.phone }}
                                </p>
                                <p class="text-sm text-gray-400">
                                    {{ selectedOrder.address }}
                                </p>
                            </div>

                            <!-- ORDER INFO -->
                            <div
                                class="bg-black/20 border border-white/10 rounded-xl p-4"
                            >
                                <h3 class="text-sm text-gray-400 mb-2">
                                    Order Info
                                </h3>
                                <p class="text-sm capitalize">
                                    Payment: {{ selectedOrder.payment_method }}
                                </p>
                                <p class="text-sm capitalize">
                                    Order Type: {{ selectedOrder.order_type }}
                                </p>
                                <p class="text-sm font-semibold text-red-400">
                                    Total: ₱{{ selectedOrder.total }}
                                </p>
                            </div>

                            <!-- ITEMS -->
                            <div
                                class="bg-black/20 border border-white/10 rounded-xl p-4"
                            >
                                <h3 class="text-sm text-gray-400">Items</h3>

                                <div class="divide-y divide-white/10">
                                    <div
                                        v-for="(item, i) in selectedOrder.items"
                                        :key="i"
                                        class="py-2 flex justify-between text-sm"
                                    >
                                        <span>{{ item.name }}</span>
                                        <span class="text-gray-400"
                                            >x{{ item.quantity }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-2 pt-2">
                                <button
                                    class="py-2 text-sm rounded-lg bg-blue-700 hover:bg-blue-800 transition"
                                    @click="updateStatus('preparing')"
                                >
                                    Preparing
                                </button>
                                <button
                                    class="py-2 text-sm rounded-lg transition"
                                    :class="
                                        selectedOrder.order_type === 'delivery'
                                            ? 'bg-purple-700 hover:bg-purple-800'
                                            : 'bg-orange-600 hover:bg-orange-700'
                                    "
                                    @click="
                                        updateStatus(
                                            selectedOrder.order_type ===
                                                'delivery'
                                                ? 'out_for_delivery'
                                                : 'ready_for_pickup',
                                        )
                                    "
                                >
                                    {{
                                        selectedOrder.order_type === "delivery"
                                            ? "Out for delivery"
                                            : "Ready for pickup"
                                    }}
                                </button>

                                <button
                                    class="py-2 text-sm rounded-lg bg-green-700 hover:bg-green-800 transition"
                                    @click="updateStatus('completed')"
                                >
                                    Complete
                                </button>

                                <button
                                    class="py-2 text-sm rounded-lg bg-red-700 hover:bg-red-800 transition"
                                    @click="updateStatus('cancelled')"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
