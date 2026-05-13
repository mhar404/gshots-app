<script setup></script>
<template>
    <section
        class="relative py-24 px-6 bg-cover bg-center overflow-hidden bg-[url('/assets/menu-bg.jpg')] font-sans"
    >
        <!-- Darker Overlay for better readability -->
        <div class="absolute inset-0 bg-black/80 z-0"></div>

        <div class="relative z-10 max-w-6xl mx-auto">
            <!-- SECTION 1: OUR STORY (Centered & Focused) -->
            <div class="text-center mb-24 max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-8">
                    <span
                        class="bg-linear-to-r from-red-500 via-orange-400 to-yellow-400 bg-clip-text text-transparent"
                    >
                        OUR STORY
                    </span>
                </h1>
                <div class="relative group">
                    <!-- Subtle glow effect behind the card -->
                    <div
                        class="absolute -inset-1 bg-linear-to-r from-red-500 to-yellow-400 rounded-3xl blur opacity-10 group-hover:opacity-20 transition duration-1000"
                    ></div>

                    <div
                        class="relative bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-10 shadow-2xl"
                    >
                        <p
                            class="text-gray-200 leading-relaxed text-lg md:text-xl font-light"
                        >
                            G Shot Café was created with one goal in mind — to
                            serve
                            <span class="text-white font-medium"
                                >high-quality milk teas, coffee, pizzas,
                                burgers, and rice meals</span
                            >
                            that satisfy every craving.
                        </p>
                        <div
                            class="w-16 h-1 bg-linear-to-r from-red-500 to-yellow-400 mx-auto my-6"
                        ></div>
                        <p class="text-gray-400 leading-relaxed text-base">
                            We believe that food is more than just a meal — it’s
                            an experience. Every order is prepared with care,
                            using fresh ingredients and efficient service.
                        </p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: MISSION & VISION (Side-by-Side Cards) -->
            <div class="mb-24">
                <div class="flex items-center gap-4 mb-10">
                    <h2
                        class="text-2xl sm:text-3xl font-bold whitespace-nowrap"
                    >
                        <span
                            class="bg-linear-to-r from-red-500 via-orange-400 to-yellow<-400 bg-clip-text text-transparent uppercase tracking-widest"
                        >
                            Our Purpose
                        </span>
                    </h2>
                    <div class="h-px w-full bg-white/10"></div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Mission -->
                    <div
                        class="group bg-white/5 hover:bg-white/10 transition-colors border border-white/10 rounded-2xl p-8"
                    >
                        <div
                            class="w-12 h-12 mb-6 flex items-center justify-center rounded-full bg-red-500/10 text-red-500 font-bold text-xl border border-red-500/20"
                        >
                            M
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">
                            Our Mission
                        </h3>
                        <p class="text-gray-400 text-base leading-relaxed">
                            To provide fast, reliable, and high-quality food and
                            beverages while delivering a smooth and enjoyable
                            customer experience through our modern ordering
                            system.
                        </p>
                    </div>

                    <!-- Vision -->
                    <div
                        class="group bg-white/5 hover:bg-white/10 transition-colors border border-white/10 rounded-2xl p-8"
                    >
                        <div
                            class="w-12 h-12 mb-6 flex items-center justify-center rounded-full bg-yellow-500/10 text-yellow-500 font-bold text-xl border border-yellow-500/20"
                        >
                            V
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">
                            Our Vision
                        </h3>
                        <p class="text-gray-400 text-base leading-relaxed">
                            To become a trusted and loved café brand known for
                            quality, innovation, and excellent service in every
                            community we serve.
                        </p>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: FEATURES (Modern Icon-style Grid) -->
            <div>
                <h2 class="text-center text-3xl font-extrabold mb-12">
                    <span
                        class="bg-linear-to-r from-red-500 via-orange-400 to-yellow-400 bg-clip-text text-transparent"
                    >
                        THE G-SHOT DIFFERENCE
                    </span>
                </h2>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        class="bg-black/40 border border-white/5 rounded-2xl p-8 hover:border-orange-500/50 transition-all duration-300"
                    >
                        <h4 class="text-white font-bold text-lg mb-2">
                            Fresh Ingredients
                        </h4>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            We use only high-quality and fresh ingredients in
                            every product we serve.
                        </p>
                    </div>

                    <div
                        class="bg-black/40 border border-white/5 rounded-2xl p-8 hover:border-orange-500/50 transition-all duration-300"
                    >
                        <h4 class="text-white font-bold text-lg mb-2">
                            Fast & Efficient
                        </h4>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Our ordering system ensures quick preparation and
                            smooth transactions.
                        </p>
                    </div>

                    <div
                        class="bg-black/40 border border-white/5 rounded-2xl p-8 hover:border-orange-500/50 transition-all duration-300 sm:col-span-2 lg:col-span-1"
                    >
                        <h4 class="text-white font-bold text-lg mb-2">
                            Customer Focused
                        </h4>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Your satisfaction is our priority — from first click
                            to final bite.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
