<script setup>
import { ref, onMounted, onUnmounted, computed, onBeforeUnmount } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useUiStore } from "@/stores/ui";
import { useCartStore } from "@/stores/cart";
import { useRouter } from "vue-router";
import { useOrderStore } from "@/stores/order";

const orderStore = useOrderStore();
const pendingCount = computed(
    () => orderStore.orders.filter((o) => o.status === "pending").length,
);
const ui = useUiStore();
const router = useRouter();

const cart = useCartStore();

const openCart = () => {
    if (auth.user) {
        ui.openCartModal();
    }
};

const currentTime = ref("");

let timer;

const updateTime = () => {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
};

const isOpen = ref(false); // mobile menu
const isScrolled = ref(false); // header scroll effect
const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

// --- AUTH MODAL STATE ---
const auth = useAuthStore();

const signOut = async () => {
    ui.showAuthToast("Logged out successfully", "logout");
    cart.items = [];
    await auth.logout();
    router.push({ name: "home" });
    isOpen.value = false;
};

const openAuth = (mode = "login") => {
    ui.openAuthModal(mode);
    isOpen.value = false;
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isDropdownOpen.value = false;
    }
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const navLinks = computed(() => {
    const links = [
        { name: "home", label: "Home" },
        { name: "menu", label: "Menu" },
        { name: "about", label: "About" },
        { name: "contact", label: "Contact" },
    ];

    // ✅ Only show Order History if NOT admin
    if (!auth.isAdmin && auth.user) {
        links.push({ name: "order-history", label: "Order History" });
    }

    return links;
});

onMounted(async () => {
    updateTime();
    timer = setInterval(updateTime, 1000);
    window.addEventListener("scroll", handleScroll);
    window.addEventListener("click", handleClickOutside);

    // prevent duplicate listeners
    if (!window.Echo) {
        console.error("Echo not initialized");
        return;
    }

    try {
        if (localStorage.getItem("token")) {
            await auth.getUser();
            await orderStore.fetchOrders();

            if (auth.isAdmin && !router.currentRoute.value.path.startsWith('/admin')) {
                router.push('/admin');
            }
        }
    } catch (err) {
        console.error("Failed to get user:", err);
    }
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
    window.removeEventListener("click", handleClickOutside);
    clearInterval(timer);
});
</script>

<template>
    <nav
        :class="[
            'w-full fixed top-0 z-50 transition-all duration-300',
            isScrolled || isOpen
                ? 'bg-black/40 backdrop-blur-sm  text-white'
                : 'bg-transparent text-white',
        ]"
    >
        <div
            class="mx-auto max-w-7xl flex items-center justify-between px-6 py-2"
        >
            <!-- Logo -->
            <div class="flex items-center">
                <RouterLink :to="{ name: 'home' }" class="cursor-pointer">
                    <img
                        src="/assets/gshot-logo.png"
                        alt="G Shot"
                        class="h-14 w-auto object-contain transition-transform hover:scale-110"
                    />
                </RouterLink>
            </div>

            <!-- Desktop Links -->
            <div class="hidden md:flex space-x-8 font-sm lg:ml-22">
                <RouterLink
                    :to="{ name: 'home' }"
                    class="hover:text-red-600 transition"
                >
                    HOME
                </RouterLink>

                <RouterLink
                    :to="{ name: 'menu' }"
                    class="hover:text-red-600 transition"
                >
                    MENU
                </RouterLink>

                <RouterLink
                    :to="{ name: 'about' }"
                    class="hover:text-red-600 transition"
                >
                    ABOUT
                </RouterLink>
                <RouterLink
                    :to="{ name: 'contact' }"
                    class="hover:text-red-600 transition"
                >
                    CONTACT
                </RouterLink>
                <RouterLink
                    v-if="auth.isAdmin"
                    to="/admin"
                    class="relative flex items-center gap-2 hover:text-red-600 transition font-bold text-red-500"
                >
                    <i class="pi pi-cog text-xl"></i>
                    DASHBOARD
                </RouterLink>
            </div>

            <!-- Right Actions -->
            <div class="hidden md:flex items-center gap-8 relative">
                <div class="text-sm text-white/80 font-mono">
                    {{ currentTime }}
                </div>
                <button
                    v-if="!auth.isAdmin"
                    @click="openCart()"
                    class="relative hover:text-red-600 transition cursor-pointer"
                >
                    <i class="pi pi-shopping-cart text-2xl"></i>

                    <span
                        v-if="cart.cartCount > 0 && auth.user"
                        class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full"
                    >
                        {{ cart.cartCount }}
                    </span>
                </button>
                <!-- NOT LOGGED IN -->
                <button
                    v-if="!auth.user"
                    @click="openAuth('login')"
                    class="relative hover:text-red-600 transition cursor-pointer"
                >
                    <i class="pi pi-user" style="font-size: 1.5rem"></i>
                </button>

                <!-- LOGGED IN -->
                <div v-else class="relative" ref="dropdownRef">
                    <button
                        @click="isDropdownOpen = !isDropdownOpen"
                        class="flex items-center hover:text-red-600 transition cursor-pointer"
                    >
                        <i class="pi pi-user" style="font-size: 1.5rem"></i>
                    </button>

                    <!-- DROPDOWN -->
                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="transform opacity-0 scale-95 translate-y-2"
                        enter-to-class="transform opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="transform opacity-100 scale-100 translate-y-0"
                        leave-to-class="transform opacity-0 scale-95 translate-y-2"
                    >
                        <div
                            v-if="isDropdownOpen"
                            class="absolute right-0 mt-4 w-50 bg-black/80 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl overflow-hidden"
                        >
                            <!-- USER INFO HEADER -->
                            <div
                                class="p-3 border-b border-white/10 bg-linear-to-br from-white/5 to-transparent"
                            >
                                <p
                                    class="text-xs text-gray-400 uppercase tracking-widest mb-1"
                                >
                                    Signed in as
                                </p>
                                <p
                                    class="text-base font-bold text-white truncate shadow-sm"
                                >
                                    {{ auth.user?.name }}
                                </p>
                            </div>

                            <!-- MENU ITEMS -->
                            <div class="space-y-1">
                                <RouterLink
                                    v-if="!auth.isAdmin"
                                    :to="{ name: 'order-history' }"
                                    @click="isDropdownOpen = false"
                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200 flex items-center gap-3 cursor-pointer group border-b border-white/10 mb-0"
                                >
                                    <div
                                        class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-white/20 transition-colors"
                                    >
                                        <i
                                            class="pi pi-history text-sm text-gray-400 group-hover:text-white transition-colors"
                                        ></i>
                                    </div>
                                    <span class="font-medium"
                                        >Order History</span
                                    >
                                </RouterLink>

                                <!-- Logout -->
                                <button
                                    @click="
                                        isDropdownOpen = false;
                                        signOut();
                                    "
                                    class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all duration-200 flex items-center gap-3 cursor-pointer group"
                                >
                                    <div
                                        class="w-8 h-8 rounded-full bg-red-500/5 flex items-center justify-center group-hover:bg-red-500/20 transition-colors"
                                    >
                                        <i
                                            class="pi pi-sign-out text-sm text-red-500/80 group-hover:text-red-400 transition-colors"
                                        ></i>
                                    </div>
                                    <span class="font-medium">Log Out</span>
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
            <div class="md:hidden flex items-center gap-6">
                <RouterLink
                    v-if="auth.isAdmin"
                    to="/admin"
                    class="relative flex items-center gap-2 hover:text-red-600 transition text-red-500"
                >
                    <i class="pi pi-cog text-2xl"></i>
                </RouterLink>

                <!-- CART -->
                <button
                    v-if="!auth.isAdmin"
                    @click="openCart()"
                    class="relative hover:text-red-600 transition cursor-pointer"
                >
                    <i class="pi pi-shopping-cart text-2xl"></i>

                    <span
                        v-if="cart.cartCount > 0 && auth.user"
                        class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full"
                    >
                        {{ cart.cartCount }}
                    </span>
                </button>

                <!-- MENU BUTTON -->
                <button
                    @click="isOpen = !isOpen"
                    class="md:hidden focus:outline-none transition hover:text-red-600"
                >
                    <i
                        v-if="!isOpen"
                        class="pi pi-bars"
                        style="font-size: 1.5rem"
                    ></i>

                    <i v-else class="pi pi-times" style="font-size: 1.5rem"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-show="isOpen" class="md:hidden px-4 pb-6">
            <div class="px-5 text-white">
                <!-- NAV LINKS -->
                <div class="flex flex-col divide-y divide-white/10">
                    <RouterLink
                        v-for="link in navLinks"
                        :key="link.name"
                        @click="isOpen = false"
                        :to="{ name: link.name }"
                        class="py-3 text-base font-medium hover:text-red-500 transition flex justify-between items-center"
                    >
                        {{ link.label }}
                        <i class="pi pi-angle-right text-sm opacity-60"></i>
                    </RouterLink>
                </div>

                <!-- USER / AUTH SECTION -->
                <div class="pt-4 border-t border-white/10 space-y-4">
                    <!-- USER INFO -->
                    <div v-if="auth.user" class="text-center">
                        <p class="text-sm text-gray-400">Welcome back</p>
                        <p class="font-semibold text-lg">
                            {{ auth.user.name }}
                        </p>
                    </div>

                    <!-- ACTION BUTTON -->
                    <button
                        v-if="!auth.user"
                        @click="openAuth('login')"
                        class="w-full py-2.5 rounded-xl bg-white text-black font-medium flex items-center justify-center gap-2 hover:bg-gray-200 transition"
                    >
                        <i class="pi pi-user"></i>
                        Sign In
                    </button>

                    <button
                        v-else
                        @click="signOut"
                        class="w-full py-2.5 rounded-xl bg-red-500/20 text-red-500 font-medium flex items-center justify-center gap-2 hover:bg-red-500/20 transition"
                    >
                        <i class="pi pi-sign-out"></i>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>
