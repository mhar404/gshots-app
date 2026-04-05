<script setup>
import { useAuthStore } from "../stores/auth";
const auth = useAuthStore();

const props = defineProps({
    product: { type: Object, default: null },
    isAddCard: { type: Boolean, default: false },
    isAdmin: { type: Boolean, default: false },
});

const emit = defineEmits(["add-to-cart", "admin-options", "add-product"]);

function onAddToCart(product) {
    emit("add-to-cart", product);
}

function onAdminOptions(product) {
    emit("admin-options", product);
}

function onAddProduct() {
    emit("add-product");
}
</script>

<template>
    <div
        class="border border-white/10 bg-white/5 rounded-xl overflow-hidden transition duration-300 hover:scale-105 flex flex-col h-full"
    >
        <!-- ADD PRODUCT CARD -->
        <div
            v-if="isAddCard"
            @click="onAddProduct"
            class="flex flex-col items-center justify-center h-full cursor-pointer border-2 border-dashed border-white/30 hover:border-red-500 text-white rounded-xl"
        >
            <i class="text-2xl sm:text-3xl mb-2 pi pi-plus"></i>
            <p class="text-sm sm:text-base font-semibold">Add Product</p>
        </div>

        <!-- NORMAL PRODUCT CARD -->
        <template v-else>
            <div class="overflow-hidden relative">
                <img
                    :src="product.image_url"
                    :alt="product.name"
                    class="w-full h-40 sm:h-44 md:h-52 lg:h-56 object-cover transition duration-500 hover:scale-110"
                />
                <button
                    v-if="isAdmin"
                    @click="onAdminOptions(product)"
                    class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-full bg-black/50 text-white hover:bg-black/70 text-lg sm:text-xl cursor-pointer"
                >
                    ⋮
                </button>
            </div>

            <div class="p-3 sm:p-4 md:p-5 text-gray-200 flex flex-col grow">
                <!-- Product Name -->
                <div class="mb-2 flex flex-col gap-1">
                    <h3
                        class="text-sm sm:text-base md:text-lg font-semibold line-clamp-2"
                    >
                        {{ product.name }}
                    </h3>
                </div>

                <!-- Price -->
                <div class="flex justify-between items-center mb-3">
                    <span
                        class="text-red-400 font-bold text-sm sm:text-base md:text-lg"
                    >
                        ₱{{ product.price }}
                    </span>
                </div>

                <!-- Add to Cart Button -->
                <button
                    :disabled="auth.isAdmin"
                    @click="onAddToCart(product)"
                    class="mt-auto w-full flex items-center justify-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 rounded-full bg-red-600 hover:bg-red-700 text-white font-medium text-sm md:text-base transition-all duration-200 whitespace-nowrap cursor-pointer"
                >
                    <i
                        class="pi pi-shopping-cart text-sm sm:text-base md:text-lg hidden xs:inline"
                    ></i>

                    <span class="truncate">
                        {{ auth.isAdmin ? "Admin Mode" : "Add to Cart" }}
                    </span>
                </button>
            </div>
        </template>
    </div>
</template>
