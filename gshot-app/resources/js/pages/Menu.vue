<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";

import ProductCard from "../components/ProductCard.vue";
import MenuFilters from "../components/MenuFilters.vue";
import ConfirmCartModal from "../components/ConfirmCartModal.vue";
import AdminProductModal from "../components/AdminProductModal.vue";

import { useAuthStore } from "../stores/auth";
import { useProductStore } from "../stores/product";
import { useUiStore } from "../stores/ui";

const productStore = useProductStore();
const auth = useAuthStore();

const router = useRouter();
const route = useRoute();

// References to modals
const confirmCartModalRef = ref(null);
const adminProductModalRef = ref(null);

const categories = ["All", "Drinks", "Burgers", "Pizzas", "RiceMeals"];
const selectedCategory = ref(route.query.category || "All");
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

const openCartModal = (item) => {
    confirmCartModalRef.value?.open(item);
};

const openAdminModal = (item = null) => {
    adminProductModalRef.value?.open(item);
};

const handleCategorySelect = (cat) => {
    selectedCategory.value = cat;
    router.replace({
        query: {
            ...route.query,
            category: cat === "All" ? undefined : cat,
        },
    });
};

onMounted(async () => {
    await productStore.fetchProducts();
});

watch(
    () => route.query.category,
    (newCat) => {
        if (newCat) {
            selectedCategory.value = newCat;
        } else {
            selectedCategory.value = "All";
        }
    },
);
</script>

<template>
    <section
        class="py-24 px-6 relative bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center min-h-screen"
    >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="max-w-7xl mx-auto relative z-10 px-4">
            <!-- Header Section -->
            <div class="mb-10 text-center animate-fade-in">
                <h1
                    class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight"
                >
                    <span
                        class="bg-linear-to-r from-red-500 via-orange-500 to-yellow-500 bg-clip-text text-transparent"
                    >
                        Our Menu
                    </span>
                </h1>
                <p class="text-gray-300 max-w-2xl mx-auto text-md md:text-xl">
                    Explore our finest selection of food and drinks. Click on
                    any item to view details and add it to your order.
                </p>

                <!-- Admin "Add Product" button -->
                <button
                    v-if="auth.isAdmin"
                    @click="openAdminModal()"
                    class="mt-6 px-6 py-2.5 rounded-full bg-red-600 hover:bg-red-700 text-white font-semibold transition cursor-pointer shadow-lg inline-flex items-center gap-2"
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
                <!-- Vue <transition-group> -->
                <ProductCard
                    v-for="(product, index) in filteredProducts"
                    :key="product.id"
                    :product="product"
                    :isAdmin="auth.isAdmin"
                    @add-to-cart="openCartModal"
                    @admin-options="openAdminModal"
                />

                <!-- No Results State -->
                <div
                    v-if="filteredProducts.length === 0"
                    class="col-span-full py-20 text-center flex flex-col items-center"
                >
                    <i
                        class="pi pi-box text-6xl text-gray-400 mb-4 opacity-50"
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
    </section>

    <!-- Modals -->
    <ConfirmCartModal ref="confirmCartModalRef" />
    <AdminProductModal ref="adminProductModalRef" />
</template>
