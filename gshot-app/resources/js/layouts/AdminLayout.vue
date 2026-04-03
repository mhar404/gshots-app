<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useOrderStore } from "@/stores/order";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const orderStore = useOrderStore();
const router = useRouter();

const pendingCount = computed(
    () => orderStore.orders.filter((o) => o.status === "pending").length,
);

const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);

const handleScroll = (e) => {
    isScrolled.value = e.target.scrollTop > 20;
};

const logout = async () => {
    await authStore.logout();
    router.push({ name: "home" });
};
</script>

<template>
    <div
        class="flex h-screen overflow-hidden bg-[url('/assets/menu-bg.jpg')] text-white relative"
    >
        <div class="absolute inset-0 bg-black/60"></div>
        <!-- Mobile Header -->
        <header
            :class="[
                'md:hidden fixed top-0 left-0 w-full h-18 z-40 transition-all duration-300 flex items-center justify-between px-6 border-b',
                isScrolled || isMobileMenuOpen
                    ? 'bg-black/40 backdrop-blur-sm text-white border-white/10'
                    : 'bg-transparent text-white border-transparent',
            ]"
        >
            <div class="flex items-center">
                <RouterLink :to="{ name: 'home' }" class="cursor-pointer">
                    <img
                        src="/assets/gshot-logo.png"
                        alt="G Shot"
                        class="h-14 w-auto object-contain transition-transform hover:scale-110"
                    />
                </RouterLink>
            </div>
            <div class="flex items-center gap-4">
                <RouterLink
                    to="/admin/order"
                    class="relative md:hidden text-white hover:text-red-500 transition focus:outline-none"
                >
                    <i class="pi pi-book" style="font-size: 1.5rem"></i>
                    <span
                        v-if="pendingCount > 0"
                        class="absolute -top-1 -right-2 bg-red-600 text-white text-[10px] font-bold min-w-[20px] h-5 flex items-center justify-center px-1 rounded-full shadow-lg"
                    >
                        {{ pendingCount }}
                    </span>
                </RouterLink>

                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="text-white hover:text-red-500 transition focus:outline-none"
                >
                    <i
                        class="pi"
                        :class="isMobileMenuOpen ? 'pi-times' : 'pi-bars'"
                        style="font-size: 1.5rem"
                    ></i>
                </button>
            </div>
        </header>

        <!-- Mobile Overlay -->
        <transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-300"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isMobileMenuOpen"
                @click="isMobileMenuOpen = false"
                class="fixed inset-0 bg-black/60 z-40 md:hidden backdrop-blur-sm"
            ></div>
        </transition>

        <!-- Sidebar -->
        <aside
            :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed md:relative top-0 left-0 w-64 border-r border-white/10 flex flex-col h-full z-50 transition-transform duration-300 md:translate-x-0"
        >
            <!-- Logo area -->
            <div
                class="h-16 md:h-20 flex justify-between md:justify-center items-center px-6 border-b border-white/10"
            >
                <div class="flex items-center">
                    <RouterLink :to="{ name: 'home' }" class="cursor-pointer">
                        <img
                            src="/assets/gshot-logo.png"
                            alt="G Shot"
                            class="h-14 w-auto object-contain transition-transform hover:scale-110"
                        />
                    </RouterLink>
                </div>
                <button
                    @click="isMobileMenuOpen = false"
                    class="md:hidden text-gray-400 hover:text-white transition"
                >
                    <i class="pi pi-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 py-6 px-4 flex flex-col gap-2 overflow-y-auto">
                <RouterLink
                    @click="isMobileMenuOpen = false"
                    to="/admin/sales"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition hover:bg-white/10 text-gray-300"
                    active-class="bg-red-500/20 text-white  "
                >
                    <i
                        class="pi pi-chart-line text-xl"
                        :class="
                            $route.path.includes('/sales') ? 'text-red-400' : ''
                        "
                    ></i>
                    <span>Sales Analytics</span>
                </RouterLink>

                <RouterLink
                    @click="isMobileMenuOpen = false"
                    to="/admin/order"
                    class="flex items-center justify-between px-4 py-3 rounded-xl transition hover:bg-white/10 text-gray-300"
                    active-class="bg-red-500/20 text-white "
                >
                    <div class="flex items-center gap-3">
                        <i
                            class="pi pi-book text-xl"
                            :class="
                                $route.path.includes('/order')
                                    ? 'text-red-400'
                                    : ''
                            "
                        ></i>
                        <span>Active Orders</span>
                    </div>

                    <!-- Badge -->
                    <span
                        v-if="pendingCount > 0"
                        class="bg-red-600 border border-red-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full shadow-[0_0_10px_rgba(220,38,38,0.5)] animate-pulse"
                    >
                        {{ pendingCount }}
                    </span>
                </RouterLink>

                <RouterLink
                    @click="isMobileMenuOpen = false"
                    to="/admin/complete-orders"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition hover:bg-white/10 text-gray-300"
                    active-class="bg-red-500/20 text-white "
                >
                    <i
                        class="pi pi-check-circle text-xl"
                        :class="
                            $route.path.includes('/complete-orders')
                                ? 'text-red-400'
                                : ''
                        "
                    ></i>
                    <span>Completed Orders</span>
                </RouterLink>

                <RouterLink
                    @click="isMobileMenuOpen = false"
                    to="/admin/menu"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition hover:bg-white/10 text-gray-300"
                    active-class="bg-red-500/20 text-white "
                >
                    <i
                        class="pi pi-list text-xl"
                        :class="
                            $route.path.includes('/menu') ? 'text-red-400' : ''
                        "
                    ></i>
                    <span>Product Menu</span>
                </RouterLink>
            </nav>

            <!-- Bottom -->
            <div class="p-4 border-t border-white/10 flex flex-col gap-2">
                <button
                    @click="logout"
                    class="flex justify-center items-center gap-2 py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 transition font-medium cursor-pointer text-white shadow-lg"
                >
                    <i class="pi pi-sign-out"></i> <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main
            @scroll="handleScroll"
            class="flex-1 h-full overflow-y-auto relative w-full pt-16 md:pt-0"
        >
            <RouterView />
        </main>
    </div>
</template>
