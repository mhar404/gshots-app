<script setup>
import { useCartStore } from "@/stores/cart";
const cart = useCartStore();
</script>

<template>
    <div
        class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 sm:p-6 flex flex-col shadow-lg"
    >
        <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-white">
            Your Orders
        </h2>

        <!-- Items -->
        <div
            class="space-y-4 overflow-y-auto max-h-[55vh] sm:max-h-[60vh] pr-2 text-white"
        >
            <div
                v-for="item in cart.items"
                :key="item.product_id"
                class="flex justify-between items-center border-b border-white/10 pb-3"
            >
                <div>
                    <p class="font-medium text-white/90 text-sm sm:text-base">
                        {{ item.product.name }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        ₱{{ item.product.price }} × {{ item.quantity }}
                    </p>
                </div>

                <div class="font-semibold text-white/90 text-sm sm:text-base">
                    ₱{{ item.product.price * item.quantity }}
                </div>
            </div>
        </div>

        <!-- Total -->
        <div
            class="mt-5 border-t border-white/10 pt-4 flex justify-between items-center text-base sm:text-lg font-semibold sticky bottom-0 text-white"
        >
            <span class="text-white/70">Total</span>
            <span class="text-red-500 text-lg sm:text-xl">
                ₱{{ cart.cartTotal }}
            </span>
        </div>
    </div>
</template>
