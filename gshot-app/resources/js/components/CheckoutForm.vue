<script setup>
import { ref } from "vue";
import { useCartStore } from "@/stores/cart";
import { useOrderStore } from "@/stores/order";
import { useRouter } from "vue-router";

const router = useRouter();
const cart = useCartStore();
const orderStore = useOrderStore();
const isSubmitting = ref(false);

const userName = ref("");
const userPhone = ref("");
const userAddress = ref("");
const paymentMethod = ref("");
const orderType = ref("delivery");
const paymentError = ref(false);

const getLocation = () => {
    navigator.geolocation.getCurrentPosition(async (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        try {
            const res = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
            );
            const data = await res.json();
            userAddress.value = data.display_name || `${lat}, ${lng}`;
        } catch {
            userAddress.value = `${lat}, ${lng}`;
        }
    });
};

const confirmCheckout = async () => {
    if (isSubmitting.value) return;

    if (!paymentMethod.value) {
        paymentError.value = true;
        return;
    }

    paymentError.value = false;
    isSubmitting.value = true;

    const items = cart.items.map((item) => ({
        name: item.product.name,
        quantity: item.quantity,
        price: item.product.price,
    }));

    const payload = {
        name: userName.value,
        phone: userPhone.value,
        payment_method: paymentMethod.value,
        order_type: orderType.value,
        address: userAddress.value,
        items: items,
        total: cart.cartTotal,
    };

    try {
        await orderStore.createOrder(payload);

        userName.value = "";
        userPhone.value = "";
        userAddress.value = "";
        paymentMethod.value = "";
    } catch (err) {
        console.error("Failed:", orderStore.error);
    } finally {
        isSubmitting.value = false;
        cart.clearCart();
        router.push("/track-order");
    }
};
</script>

<template>
    <form
        @submit.prevent="confirmCheckout"
        class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 sm:p-6 flex flex-col gap-3 shadow-lg text-white"
    >
        <h2 class="text-xl sm:text-2xl font-semibold">Checkout Details</h2>

        <div
            v-if="orderStore.activeUserOrders.length > 0"
            class="p-4 rounded-xl bg-red-500/20 border border-red-500 text-red-100 flex items-center gap-3"
        >
            <i class="pi pi-exclamation-triangle text-2xl"></i>
            <div>
                <p class="font-bold">Active order detected!</p>
                <p class="text-sm">
                    You must wait for your current order to be completed before
                    placing a new one.
                </p>
            </div>
        </div>

        <label class="text-xs text-gray-400">Full Name</label>
        <input
            v-model="userName"
            type="text"
            placeholder="Enter your name"
            class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none text-sm sm:text-base"
            required
        />

        <label class="text-xs text-gray-400">Phone Number</label>
        <input
            v-model="userPhone"
            type="tel"
            placeholder="09XXXXXXXXX"
            class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none text-sm sm:text-base"
            required
        />

        <label class="text-xs text-gray-400">Order Type</label>
        <div class="grid grid-cols-2 gap-3">
            <div
                @click="orderType = 'delivery'"
                :class="[
                    'cursor-pointer rounded-xl border p-3 flex items-center justify-center gap-2 transition',
                    orderType === 'delivery'
                        ? 'border-red-500 ring-1 ring-red-500 bg-red-500/10 text-white'
                        : 'border-white/10 bg-white/5 text-gray-300 hover:bg-white/10',
                ]"
            >
                <i class="pi pi-truck text-xl"></i>
                <span class="text-sm">Delivery</span>
            </div>

            <div
                @click="
                    orderType = 'pickup';
                    paymentMethod = 'gcash';
                    userAddress = '';
                "
                :class="[
                    'cursor-pointer rounded-xl border p-3 flex items-center justify-center gap-2 transition',
                    orderType === 'pickup'
                        ? 'border-red-500 ring-1 ring-red-500 bg-red-500/10 text-white'
                        : 'border-white/10 bg-white/5 text-gray-300 hover:bg-white/10',
                ]"
            >
                <i class="pi pi-shop text-xl"></i>
                <span class="text-sm">Pickup</span>
            </div>
        </div>

        <!-- Delivery Address -->
        <div v-if="orderType === 'delivery'" class="gap-3 flex flex-col">
            <label class="text-xs text-gray-400">Address</label>
            <textarea
                v-model="userAddress"
                placeholder="Enter address or use location"
                class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none resize-none h-24 text-sm sm:text-base"
                required
            ></textarea>

            <p class="text-xs text-gray-400">
                Auto-filled from your location. Feel free to edit.
            </p>

            <button
                type="button"
                @click="getLocation"
                class="flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 py-2 px-4 rounded-full font-medium shadow-md hover:shadow-lg transition text-sm sm:text-base cursor-pointer"
            >
                <i class="pi pi-map-marker"></i>
                <span>Use My Location</span>
            </button>
        </div>

        <!-- Pickup Map -->
        <div v-if="orderType === 'pickup'" class="gap-3 flex flex-col">
            <label class="text-xs text-gray-400">Store Location</label>
            <div
                class="w-full h-40 rounded-xl border border-white/10 bg-white/5 flex items-center justify-center text-gray-400"
            >
                Map Placeholder
            </div>
        </div>

        <!-- Mode of Payment -->
        <label class="text-xs text-gray-400">Mode of Payment</label>
        <div class="grid grid-cols-2 gap-3">
            <div
                @click="paymentMethod = 'gcash'"
                :class="[
                    'cursor-pointer rounded-xl border flex items-center justify-center bg-white transition py-2',
                    paymentMethod === 'gcash'
                        ? 'border-red-500 ring-2 ring-red-500'
                        : 'border-white/10',
                ]"
            >
                <img src="/icons/GCash-Logo.png" class="h-10 object-contain" />
            </div>

            <div
                v-if="orderType === 'delivery'"
                @click="paymentMethod = 'cash'"
                :class="[
                    'cursor-pointer rounded-xl border flex items-center justify-center bg-white transition py-2',
                    paymentMethod === 'cash'
                        ? 'border-red-500 ring-2 ring-red-500'
                        : 'border-white/10',
                ]"
            >
                <img src="/icons/cod-logo.png" class="h-10 object-contain" />
            </div>

            <p v-if="paymentError" class="text-red-400 text-xs mt-1 col-span-2">
                Please select a payment method
            </p>
        </div>

        <button
            type="submit"
            :disabled="isSubmitting || orderStore.activeUserOrders.length > 0"
            class="mt-4 w-full bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed py-3 rounded-xl font-semibold transition cursor-pointer"
        >
            {{ isSubmitting ? "Processing..." : "Confirm Checkout" }}
        </button>
    </form>
</template>
