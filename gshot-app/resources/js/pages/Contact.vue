<script setup>
import { ref } from "vue";

const form = ref({
    name: "",
    email: "",
    message: "",
});

const submitForm = () => {
    alert("Message sent successfully!");
    form.value.name = "";
    form.value.email = "";
    form.value.message = "";
};
</script>

<template>
    <section
        class="relative py-28 px-6 bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center overflow-hidden"
    >
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black/70 z-0"></div>

        <!-- Content Wrapper -->
        <div class="relative z-10 max-w-6xl mx-auto">
            <!-- ===================== -->
            <!-- 1️⃣ CONTACT HEADER -->
            <!-- ===================== -->
            <div class="mb-16">
                <h1
                    class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-6"
                >
                    <span
                        class="bg-linear-to-r from-red-500 via-orange-400 to-yellow-400 bg-clip-text text-transparent"
                    >
                        CONTACT US
                    </span>
                </h1>

                <p
                    class="text-gray-300 text-sm sm:text-base md:text-lg max-w-2xl"
                >
                    Have questions, suggestions, or feedback? We’d love to hear
                    from you. Reach out and our team will respond as soon as
                    possible.
                </p>
            </div>

            <!-- ===================== -->
            <!-- 2️⃣ CONTACT CONTENT -->
            <!-- ===================== -->
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Contact Information -->
                <div
                    class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-xl"
                >
                    <h2 class="text-xl font-semibold text-white mb-6">
                        Get In Touch
                    </h2>

                    <div class="space-y-5 text-gray-300 text-sm sm:text-base">
                        <div class="flex items-center gap-3">
                            <i
                                class="pi pi-map-marker text-red-600 text-lg"
                            ></i>
                            <span
                                >Block 9 Lot 3, Amira Townhomes, Brgy Sinalhan,
                                Santa Rosa, Laguna</span
                            >
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="pi pi-phone text-red-600 text-lg"></i>
                            <span>+63 912 345 6789</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <i class="pi pi-envelope text-red-600 text-lg"></i>
                            <span>info@gshotcafe.com</span>
                        </div>
                    </div>

                    <!-- Business Hours -->
                    <div class="mt-8">
                        <h3 class="text-white font-semibold mb-3">
                            Business Hours
                        </h3>
                        <p class="text-gray-300 text-sm">
                            Monday - Saturday
                            <br />
                            9:00 AM – 10:00 PM
                        </p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div
                    class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 shadow-xl"
                >
                    <h2 class="text-xl font-semibold text-white mb-6">
                        Send a Message
                    </h2>

                    <form @submit.prevent="submitForm" class="space-y-5">
                        <div>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Your Name"
                                required
                                class="w-full px-4 py-3 rounded-xl bg-white/10 text-white border border-white/20 focus:outline-none focus:border-red-400 text-sm"
                            />
                        </div>

                        <div>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="Your Email"
                                required
                                class="w-full px-4 py-3 rounded-xl bg-white/10 text-white border border-white/20 focus:outline-none focus:border-red-400 text-sm"
                            />
                        </div>

                        <div>
                            <textarea
                                v-model="form.message"
                                rows="4"
                                placeholder="Your Message"
                                required
                                class="w-full px-4 py-3 rounded-xl bg-white/10 text-white border border-white/20 focus:outline-none focus:border-red-400 text-sm resize-none"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3 rounded-full bg-red-600 hover:bg-red-700 text-white font-semibold cursor-pointer"
                        >
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
