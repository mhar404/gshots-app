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
        class="bg-zinc-900/90 border border-zinc-800 rounded-xl p-6 flex flex-col gap-5 shadow-xl text-white"
    >
        <h2 class="text-xl font-bold">Checkout Details</h2>

        <div
            v-if="orderStore.activeUserOrders.length > 0"
            class="p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 flex items-start gap-3"
        >
            <i class="pi pi-exclamation-triangle mt-0.5"></i>
            <div>
                <p class="font-semibold text-sm">Active order detected</p>
                <p class="text-xs mt-1 text-red-400/80">
                    You must wait for your current order to be completed before
                    placing a new one.
                </p>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-400 mb-1 block"
                    >Full Name</label
                >
                <input
                    v-model="userName"
                    type="text"
                    placeholder="Enter your name"
                    class="w-full px-4 py-2.5 rounded-lg bg-zinc-800/50 border border-zinc-700 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none text-sm"
                    required
                />
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-400 mb-1 block"
                    >Phone Number</label
                >
                <input
                    v-model="userPhone"
                    type="tel"
                    placeholder="09XXXXXXXXX"
                    class="w-full px-4 py-2.5 rounded-lg bg-zinc-800/50 border border-zinc-700 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none text-sm"
                    required
                />
            </div>
        </div>

        <div>
            <label class="text-sm font-medium text-zinc-400 mb-2 block"
                >Order Type</label
            >
            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    @click="orderType = 'delivery'"
                    :class="[
                        'rounded-lg border p-3 flex items-center justify-center gap-2 transition-colors duration-200 cursor-pointer',
                        orderType === 'delivery'
                            ? 'border-red-500 bg-red-500/10 text-red-500'
                            : 'border-zinc-700 bg-zinc-800/50 text-zinc-400 hover:bg-zinc-800',
                    ]"
                >
                    <i class="pi pi-truck text-lg"></i>
                    <span class="text-sm font-medium">Delivery</span>
                </button>

                <button
                    type="button"
                    @click="
                        orderType = 'pickup';
                        paymentMethod = 'gcash';
                        userAddress = '';
                    "
                    :class="[
                        'rounded-lg border p-3 flex items-center justify-center gap-2 transition-colors duration-200 cursor-pointer',
                        orderType === 'pickup'
                            ? 'border-red-500 bg-red-500/10 text-red-500'
                            : 'border-zinc-700 bg-zinc-800/50 text-zinc-400 hover:bg-zinc-800',
                    ]"
                >
                    <i class="pi pi-shop text-lg"></i>
                    <span class="text-sm font-medium">Store Pickup</span>
                </button>
            </div>
        </div>

        <!-- Delivery Address -->
        <div v-if="orderType === 'delivery'">
            <label class="text-sm font-medium text-zinc-400 block"
                >Address</label
            >

            <textarea
                v-model="userAddress"
                placeholder="Enter complete address"
                class="w-full px-4 py-3 rounded-lg bg-zinc-800/50 border border-zinc-700 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none resize-none h-24 text-sm"
                required
            ></textarea>

            <div class="flex justify-between items-center gap-2 mt-2">
                <button
                    type="button"
                    @click="getLocation"
                    class="text-red-500 hover:text-red-400 text-sm flex items-center gap-1.5 font-medium transition-colors cursor-pointer"
                >
                    <i class="pi pi-map-marker"></i>
                    Use my current location
                </button>
                <p class="text-xs text-gray-400">
                    (Auto-filled from your location. Feel free to edit.)
                </p>
            </div>
        </div>

        <!-- Pickup Map -->
        <div v-if="orderType === 'pickup'">
            <label class="text-sm font-medium text-zinc-400 mb-1 block"
                >Store Location</label
            >
            <iframe
                class="w-full h-48 rounded-lg border border-zinc-700 bg-zinc-800/50"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3865.691880407137!2d121.10351467590805!3d14.329327083072225!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d90032870333%3A0x961ad3705ffa6db3!2sG'SHOT%20TEAS!5e0!3m2!1sen!2sph!4v1743753412586!5m2!1sen!2sph"
                style="border: 0"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>

        <!-- Mode of Payment -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label class="text-sm font-medium text-zinc-400 block"
                    >Payment Method</label
                >
                <span v-if="paymentError" class="text-red-400 text-xs"
                    >Required</span
                >
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    @click="paymentMethod = 'gcash'"
                    :class="[
                        'rounded-lg border flex items-center justify-center bg-white h-14 transition-colors cursor-pointer',
                        paymentMethod === 'gcash'
                            ? 'border-red-500 ring-2 ring-red-500'
                            : 'border-zinc-200',
                    ]"
                >
                    <img
                        src="/icons/GCash-Logo.png"
                        class="h-12 object-contain"
                    />
                </button>

                <button
                    type="button"
                    v-if="orderType === 'delivery'"
                    @click="paymentMethod = 'cash'"
                    :class="[
                        'rounded-lg border flex items-center justify-center bg-white h-14 transition-colors cursor-pointer',
                        paymentMethod === 'cash'
                            ? 'border-red-500 ring-2 ring-red-500'
                            : 'border-zinc-200',
                    ]"
                >
                    <img
                        src="/icons/cod-logo.png"
                        class="h-10 object-contain"
                    />
                </button>
            </div>
        </div>

        <button
            type="submit"
            :disabled="isSubmitting || orderStore.activeUserOrders.length > 0"
            class="w-full bg-red-600 hover:bg-red-700 disabled:bg-zinc-700 disabled:text-zinc-500 text-white disabled:cursor-not-allowed py-3.5 rounded-lg font-bold text-sm transition-colors mt-2 leading-none inline-flex items-center justify-center gap-2"
        >
            <i v-if="isSubmitting" class="pi pi-spinner pi-spin"></i>
            {{ isSubmitting ? "Processing..." : "Confirm Checkout" }}
        </button>
    </form>
</template>
