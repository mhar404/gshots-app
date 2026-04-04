<script setup>
import { useCartStore } from "@/stores/cart";
const cart = useCartStore();
</script>

<template>
    <div
        class="bg-zinc-900/90 border border-zinc-800 rounded-xl p-6 flex flex-col shadow-xl"
    >
        <h2 class="text-xl font-bold mb-4 text-white">Order Summary</h2>

        <!-- Items -->
        <div class="space-y-4 overflow-y-auto max-h-[55vh] pr-2 text-white">
            <div
                v-for="item in cart.items"
                :key="item.product_id"
                class="flex justify-between items-start border-b border-zinc-800 pb-4"
            >
                <div class="flex flex-col">
                    <p class="font-medium text-white text-base">
                        {{ item.product.name }}
                    </p>
                    <p class="text-sm text-zinc-400 mt-0.5">
                        <span class="text-white"
                            >₱{{ item.product.price }}</span
                        >
                        × {{ item.quantity }}
                    </p>
                </div>

                <div class="font-semibold text-white text-base">
                    ₱{{ item.product.price * item.quantity }}
                </div>
            </div>
        </div>

        <!-- Total -->
        <div
            class="mt-4 pt-4 flex justify-between items-center text-lg font-bold sticky bottom-0 text-white bg-zinc-900/90 isolate mix-blend-normal"
        >
            <span class="text-zinc-300">Total</span>
            <span class="text-red-500 text-xl"> ₱{{ cart.cartTotal }} </span>
        </div>
    </div>
</template>
