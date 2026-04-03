import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useCartStore } from "@/stores/cart";

const routes = [
    {
        path: "/",
        name: "home",
        component: () => import("@/pages/Home.vue"),
    },
    {
        path: "/menu/:category?",
        name: "menu",
        component: () => import("@/pages/Menu.vue"),
    },
    {
        path: "/about",
        name: "about",
        component: () => import("@/pages/About.vue"),
    },
    {
        path: "/contact",
        name: "contact",
        component: () => import("@/pages/Contact.vue"),
    },
    {
        path: "/checkout",
        name: "checkout",
        component: () => import("@/pages/Checkout.vue"),
        meta: { requiresAuth: true, requiresOrder: true },
    },
    {
        path: "/track-order",
        name: "track-order",
        component: () => import("@/pages/TrackOrder.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/order-history",
        name: "order-history",
        component: () => import("@/pages/OrderHistory.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/admin",
        component: () => import("@/layouts/AdminLayout.vue"),
        meta: { requiresAdmin: true },
        children: [
            {
                path: "",
                redirect: "/admin/sales"
            },
            {
                path: "order",
                name: "admin-order",
                component: () => import("@/pages/Order.vue"),
            },
            {
                path: "complete-orders",
                name: "admin-complete-orders",
                component: () => import("@/pages/CompleteOrder.vue"),
            },
            {
                path: "sales",
                name: "admin-sales",
                component: () => import("@/pages/Sales.vue"),
            },
            {
                path: "menu",
                name: "admin-menu",
                component: () => import("@/pages/AdminMenu.vue"),
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition;
        return { top: 0, behavior: "smooth" };
    },
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const cartStore = useCartStore();

    if (authStore.user && authStore.isAdmin && !to.path.startsWith("/admin")) {
        return next("/admin");
    }

    if (to.meta.requiresAuth && !authStore.user) {
        return next({ name: "home" });
    }

    if (to.meta.requiresAdmin) {
        if (!authStore.user || !authStore.isAdmin) {
            return next({ name: "home" });
        }
    }

    if (to.meta.requiresOrder && !cartStore.items.length) {
        return next({ name: "home" });
    }

    next();
});

export default router;
