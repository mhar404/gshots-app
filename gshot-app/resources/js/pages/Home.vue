<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const selectedCategory = ref(null);

/* Categories */
const categories = [
    { name: "Drinks", image: "/assets/gshot-drinks.png" },
    { name: "Burgers", image: "/assets/gshot-burgers.png" },
    { name: "Pizzas", image: "/assets/gshot-pizzas.png" },
    { name: "RiceMeals", image: "/assets/gshot-rice.png" },
];

/* Function to select category and redirect */
const selectCategory = (category) => {
    selectedCategory.value = category;
    router.push({
        name: "menu",
        query: { category: category.name },
    });
};

/* Testimonials */
const testimonials = [
    {
        id: 1,
        name: "Jennifer Lewis",
        avatar: "https://i.pravatar.cc/150?img=1",
        message: "FASTT DELIVERY GOOD SERVICE",
    },
    {
        id: 2,
        name: "Alicia Heart",
        avatar: "https://i.pravatar.cc/150?img=2",
        message: "HAHAHAHA FINGERR LICKIN GOOOD",
    },
    {
        id: 3,
        name: "Juan Carlos",
        avatar: "https://i.pravatar.cc/150?img=3",
        message: "AHHHH SARAPPP TANGINA",
    },
];
</script>

<template>
    <div
        class="relative min-h-screen bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center"
    >
        <div class="absolute inset-0 bg-black/70"></div>
        <div class="relative z-10">
            <!-- HERO SECTION -->
            <main
                class="relative min-h-screen flex items-center justify-center bg-[url('/assets/hero-bg.jpg')] bg-cover bg-center"
            >
                <div class="absolute inset-0 bg-black/60"></div>

                <div
                    class="relative z-10 w-full max-w-4xl px-6 text-center animate-fade-ins"
                >
                    <span
                        class="inline-flex flex-wrap justify-center items-center px-6 py-2 mb-4 text-xs sm:text-sm font-semibold tracking-[0.25em] uppercase text-yellow-500 border-b border-yellow-500/30"
                    >
                        Milktea • Coffee • Pizza • Burgers • Rice Meals
                    </span>

                    <h1
                        class="text-white text-5xl md:text-7xl lg:text-8xl font-extrabold leading-tight mb-6 text-center"
                    >
                        <span
                            class="bg-linear-to-r from-red-500 via-orange-500 to-yellow-500 bg-clip-text text-transparent"
                        >
                            Satisfy Yourself
                        </span>
                    </h1>

                    <p
                        class="text-gray-300 text-base md:text-lg lg:text-xl mb-10 max-w-2xl mx-auto leading-relaxed"
                    >
                        Refreshing milk teas, aromatic coffees, cheesy pizzas,
                        juicy burgers, and hearty rice meals crafted fresh for
                        every craving.
                    </p>

                    <div
                        class="flex flex-row gap-5 justify-center items-center"
                    >
                        <RouterLink
                            :to="{ name: 'menu' }"
                            class="px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base text-center cursor-pointer rounded-full font-semibold text-white border border-white/50 hover:bg-white hover:text-black transition-all duration-300 active:scale-95 hover:scale-105"
                        >
                            View Menu
                        </RouterLink>

                        <a
                            href="#collection"
                            class="px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base text-center cursor-pointer rounded-full font-semibold text-white bg-linear-to-r from-red-600 to-orange-500 hover:from-red-700 hover:to-orange-600 transition-all duration-300 active:scale-95 hover:scale-105 shadow-lg shadow-red-500/40"
                        >
                            Order Now
                        </a>
                    </div>
                </div>
            </main>

            <!-- COLLECTION -->
            <section class="relative py-15 md:py-18 px-6" id="collection">
                <div class="relative max-w-7xl mx-auto">
                    <div class="text-center mb-12">
                        <h1
                            class="text-white text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight mb-3"
                        >
                            <span
                                class="bg-linear-to-r from-red-500 via-orange-400 to-yellow-400 bg-clip-text text-transparent"
                            >
                                OUR COLLECTION
                            </span>
                        </h1>

                        <div
                            class="mx-auto w-24 h-1 rounded-full bg-linear-to-r from-red-500 via-orange-400 to-yellow-400 mb-4"
                        ></div>

                        <p
                            class="text-white/70 text-sm sm:text-base md:text-lg"
                        >
                            Explore our curated categories and find your
                            favorites
                        </p>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8"
                    >
                        <div
                            v-for="category in categories"
                            :key="category.name"
                            @click="selectCategory(category)"
                            class="relative cursor-pointer rounded-2xl overflow-hidden shadow-2xl transform transition duration-300 hover:scale-105 hover:shadow-2xl hover:ring-4 hover:ring-red-600"
                            :class="{
                                'ring-8 ring-red-600':
                                    selectedCategory === category,
                            }"
                        >
                            <img
                                :src="category.image"
                                :alt="category.name"
                                class="w-full h-64 sm:h-72 md:h-80 lg:h-96 object-cover transition-all duration-500"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- WHY CHOOSE US -->
            <section class="pb-10 px-6">
                <div class="max-w-7xl mx-auto">
                    <h2
                        class="text-2xl sm:text-3xl md:text-4xl font-bold mb-12 text-left"
                    >
                        <span
                            class="bg-clip-text text-transparent bg-linear-to-r from-red-500 via-orange-400 to-yellow-400"
                        >
                            WHY CHOOSE US
                        </span>
                    </h2>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12"
                    >
                        <!-- Fast Service -->
                        <div
                            class="relative bg-white/5 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-white/10 text-center"
                        >
                            <i
                                class="pi pi-bolt text-yellow-400 mb-4"
                                style="font-size: 2rem"
                            ></i>

                            <h4 class="font-semibold text-gray-100 mb-3">
                                Fast Service
                            </h4>

                            <p class="text-gray-300 text-sm">
                                Quick preparation and smooth ordering system to
                                satisfy your cravings instantly.
                            </p>
                        </div>

                        <!-- Quality Ingredients -->
                        <div
                            class="relative bg-white/5 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-white/10 text-center"
                        >
                            <i
                                class="pi pi-star text-orange-400 mb-4"
                                style="font-size: 2rem"
                            ></i>

                            <h4 class="font-semibold text-gray-100 mb-3">
                                Quality Ingredients
                            </h4>

                            <p class="text-gray-300 text-sm">
                                Fresh, premium ingredients used in every milk
                                tea, burger, pizza, and rice meal.
                            </p>
                        </div>

                        <!-- Easy Online Ordering -->
                        <div
                            class="relative bg-white/5 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-white/10 text-center"
                        >
                            <i
                                class="pi pi-shopping-cart text-red-600 mb-4"
                                style="font-size: 2rem"
                            ></i>

                            <h4 class="font-semibold text-gray-100 mb-3">
                                Easy Online Ordering
                            </h4>

                            <p class="text-gray-300 text-sm">
                                Simple, responsive ordering system built for
                                smooth customer experience.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- TESTIMONIALS -->
            <section class="py-10 px-6">
                <div class="max-w-7xl mx-auto">
                    <h2
                        class="text-2xl sm:text-3xl md:text-4xl font-bold mb-12 text-left"
                    >
                        <span
                            class="bg-clip-text text-transparent bg-linear-to-r from-red-500 via-orange-400 to-yellow-400"
                        >
                            WHAT OUR CUSTOMERS SAY
                        </span>
                    </h2>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12"
                    >
                        <div
                            v-for="testimonial in testimonials"
                            :key="testimonial.id"
                            class="relative bg-white/5 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-white/10 transition-transform"
                        >
                            <p
                                class="text-gray-200 mb-6 relative text-center italic before:content-[''] before:absolute before:-top-3 before:left-1/2 before:-translate-x-1/2 before:w-12 before:h-1 before:rounded-full before:bg-linear-to-r before:from-red-500 before:via-orange-400 before:to-yellow-400"
                            >
                                "{{ testimonial.message }}"
                            </p>

                            <div class="flex flex-col items-center mt-4">
                                <img
                                    :src="testimonial.avatar"
                                    :alt="testimonial.name"
                                    class="w-16 h-16 rounded-full object-cover mb-3 border-2 border-transparent p-0.5"
                                />
                                <h4
                                    class="font-semibold text-gray-100 tracking-wide"
                                >
                                    {{ testimonial.name }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="py-10 px-6 border-t border-white/10">
                <div
                    class="max-w-7xl mx-auto text-center text-gray-400 text-sm"
                >
                    © 2026 G Shot Cafe. All rights reserved.
                </div>
            </footer>
        </div>
    </div>
</template>
