<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useCartStore } from "@/stores/cart";
import { useUiStore } from "@/stores/ui";

const auth = useAuthStore();
const cart = useCartStore();
const ui = useUiStore();

const showModal = ref(false);
const selectedItem = ref(null);
const quantity = ref(1);

const open = (item) => {
    selectedItem.value = item;
    quantity.value = 1;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const confirmAddToCart = () => {
    if (!auth.user) {
        ui.openAuthModal("login");
    } else {
        cart.addToCart(selectedItem.value, quantity.value);
        ui.showCartToast("Added to cart!");
        closeModal();
    }
};

defineExpose({ open });
</script>

<template>
    <div
        v-if="showModal && selectedItem"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
    >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/70" @click="closeModal"></div>

        <!-- Modal Box -->
        <div
            class="relative bg-black/20 backdrop-blur-sm border border-white/20 rounded-xl p-8 w-full max-w-md shadow-2xl animate-fade-in text-white"
        >
            <!-- Close -->
            <button
                @click="closeModal"
                class="absolute top-2 right-3 text-gray-300 hover:text-white text-xl cursor-pointer"
            >
                ✕
            </button>

            <!-- Image -->
            <img
                :src="selectedItem.image_url"
                :alt="selectedItem.name"
                class="w-full h-48 object-cover rounded-2xl mb-5 border border-white/10"
            />

            <!-- Name -->
            <h3 class="text-xl font-semibold mb-1">
                {{ selectedItem.name }}
            </h3>

            <p class="text-gray-300 mb-3 text-sm">
                {{ selectedItem.description }}
            </p>

            <!-- Price -->
            <p class="text-red-400 font-bold text-xl mb-6">₱{{ selectedItem.price }}</p>

            <!-- Quantity -->
            <div class="flex items-center justify-between mb-6">
                <span class="text-gray-300">Quantity</span>

                <div class="flex items-center gap-4 bg-white/5 rounded-lg border border-white/10 px-2 py-1">
                    <button
                        @click="quantity > 1 ? quantity-- : null"
                        class="w-8 h-8 rounded hover:bg-white/10 flex items-center justify-center cursor-pointer transition"
                    >
                        -
                    </button>

                    <span class="font-semibold text-lg w-4 text-center">
                        {{ quantity }}
                    </span>

                    <button
                        @click="quantity++"
                        class="w-8 h-8 rounded hover:bg-white/10 flex items-center justify-center cursor-pointer transition"
                    >
                        +
                    </button>
                </div>
            </div>

            <!-- Confirm -->
            <button
                @click="confirmAddToCart"
                class="w-full py-3 rounded-xl bg-red-600 font-semibold hover:bg-red-700 cursor-pointer shadow-lg hover:shadow-red-900/50 transition-all flex items-center justify-center gap-2"
            >
                <i class="pi pi-cart-plus text-lg"></i>
                <span>Add to Cart - ₱{{ selectedItem.price * quantity }}</span>
            </button>
        </div>
    </div>
</template>
