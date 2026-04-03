<script setup>
const props = defineProps({
    selectedCategory: String,
    searchQuery: String,
    sortOption: String,
});

const emit = defineEmits([
    "update:searchQuery",
    "update:sortOption",
    "select-category",
]);

const categories = ["All", "Drinks", "Burgers", "Pizzas", "RiceMeals"];
</script>

<template>
    <!-- Category Buttons & Search -->
    <div
        class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-14"
    >
        <!-- Categories -->
        <div class="flex flex-wrap justify-center lg:justify-start gap-4">
            <button
                v-for="cat in categories"
                :key="cat"
                @click="emit('select-category', cat)"
                class="relative overflow-hidden px-6 py-2 rounded-full text-sm md:text-base cursor-pointer before:absolute before:inset-0 before:bg-red-600 before:scale-x-0 before:origin-left before:transition-transform before:duration-500 before:ease-out hover:before:scale-x-100 transition"
                :class="
                    selectedCategory === cat
                        ? 'bg-red-600 text-white before:hidden shadow-lg shadow-red-500/30'
                        : 'bg-white/10 text-gray-200 hover:bg-white/20'
                "
            >
                <span class="relative z-10">{{ cat }}</span>
            </button>
        </div>

        <!-- Search + Sort -->
        <div
            class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-end"
        >
            <!-- Search -->
            <div class="relative">
                <i
                    class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-white/50"
                ></i>
                <input
                    :value="searchQuery"
                    @input="emit('update:searchQuery', $event.target.value)"
                    type="text"
                    placeholder="Search menu..."
                    class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-full bg-white/10 text-white border border-transparent focus:outline-none focus:border-red-600 focus:bg-white/20 transition-all placeholder-white/50"
                />
            </div>

            <!-- Sort Dropdown -->
            <select
                :value="sortOption"
                @change="emit('update:sortOption', $event.target.value)"
                class="px-5 py-2 rounded-full bg-white/10 text-white border border-transparent focus:outline-none focus:border-red-600 cursor-pointer appearance-none outline-none transition-all hover:bg-white/20"
            >
                <option value="default" class="bg-gray-900 text-white">
                    Sort by Price
                </option>
                <option value="low-high" class="bg-gray-900 text-white">
                    Lowest to Highest
                </option>
                <option value="high-low" class="bg-gray-900 text-white">
                    Highest to Lowest
                </option>
            </select>
        </div>
    </div>
</template>
