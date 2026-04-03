<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";

import ProductCard from "../components/ProductCard.vue";
import MenuFilters from "../components/MenuFilters.vue";
import AdminProductModal from "../components/AdminProductModal.vue";

import { useAuthStore } from "../stores/auth";
import { useProductStore } from "../stores/product";

const productStore = useProductStore();
const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const adminProductModalRef = ref(null);

const categories = ["All", "Drinks", "Burgers", "Pizzas", "RiceMeals"];
const selectedCategory = ref("All");
const searchQuery = ref("");
const sortOption = ref("default");

const filteredProducts = computed(() => {
    let list = productStore.products;

    if (selectedCategory.value !== "All") {
        list = list.filter((p) => {
            return (
                p.category &&
                p.category.toLowerCase() ===
                    selectedCategory.value.toLowerCase()
            );
        });
    }

    if (searchQuery.value) {
        list = list.filter((p) =>
            p.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
        );
    }

    switch (sortOption.value) {
        case "low-high":
            return list.slice().sort((a, b) => a.price - b.price);
        case "high-low":
            return list.slice().sort((a, b) => b.price - a.price);
        default:
            return list;
    }
});

const openAdminModal = (item = null) => {
    adminProductModalRef.value?.open(item);
};

const handleCategorySelect = (cat) => {
    selectedCategory.value = cat;
};

onMounted(async () => {
    await productStore.fetchProducts();
});
</script>

<template>
    <section class="min-h-screen py-10 px-6 relative text-white">
        <!-- Overlay if you want to reuse the menu background approach -->
        <div
            class="absolute inset-0 bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center"
        ></div>
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="max-w-6xl mx-auto relative z-10 px-4">
            <!-- Header Section -->
            <div
                class="mb-10 flex flex-col md:flex-row justify-between items-center md:items-end gap-6"
            >
                <div class="space-y-3">
                    <!-- Section badge -->
                    <div class="flex items-center gap-2">
                        <span
                            class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"
                        ></span>
                        <span
                            class="text-xs uppercase tracking-wider text-white/60"
                        >
                            Management
                        </span>
                    </div>

                    <!-- Title -->
                    <h1
                        class="text-4xl font-bold leading-tight bg-linear-to-r from-red-500 via-orange-500 to-yellow-500 bg-clip-text text-transparent"
                    >
                        Product Management
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-sm text-white/70 max-w-md leading-relaxed">
                        Create, update, or remove menu items to keep your
                        catalog up to date and organized.
                    </p>
                </div>

                <button
                    @click="openAdminModal()"
                    class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition cursor-pointer shadow-lg inline-flex items-center gap-2"
                >
                    <i class="pi pi-plus"></i> Add Product
                </button>
            </div>

            <MenuFilters
                v-model:searchQuery="searchQuery"
                v-model:sortOption="sortOption"
                :selectedCategory="selectedCategory"
                @select-category="handleCategorySelect"
            />

            <!-- Product Grid -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8 min-h-[50vh]"
            >
                <ProductCard
                    v-for="(product, index) in filteredProducts"
                    :key="product.id"
                    :product="product"
                    :isAdmin="auth.isAdmin"
                    @admin-options="openAdminModal"
                />

                <div
                    v-if="filteredProducts.length === 0"
                    class="col-span-full py-20 text-center flex flex-col items-center"
                >
                    <i
                        class="pi pi-box text-6xl text-gray-500 mb-4 opacity-50"
                    ></i>
                    <p class="text-gray-400 text-xl font-medium">
                        No items found matching your criteria.
                    </p>
                    <button
                        @click="
                            searchQuery = '';
                            selectedCategory = 'All';
                            sortOption = 'default';
                        "
                        class="mt-4 text-red-500 hover:underline cursor-pointer"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <AdminProductModal ref="adminProductModalRef" />
    </section>
</template>
