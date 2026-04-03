import { defineStore } from "pinia";
import api from "@/lib/axios";
import { useCartStore } from "@/stores/cart";
import { useOrderStore } from "@/stores/order";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        errors: {},
        loading: false,
    }),
    getters: {
        isAdmin: (state) => state.user?.role === "admin",
    },

    actions: {
        clearErrors() {
            this.errors = {};
        },

        async login(formData) {
            await this.authenticate("login", formData);
        },

        async register(formData) {
            await this.authenticate("register", formData);
        },

        async authenticate(apiRoute, formData) {
            try {
                const res = await api.post(`/${apiRoute}`, formData);
                this.user = res.data.user;
                localStorage.setItem("token", res.data.token);
                localStorage.setItem("user", JSON.stringify(res.data.user));
                this.errors = {};
                console.log(res.data);

                const cartStore = useCartStore();
                await cartStore.fetchCart();

                const orderStore = useOrderStore();
                if (this.isAdmin) {
                    await orderStore.fetchOrders();
                } else {
                    await orderStore.fetchOrders(); // Assuming users might track their order too
                }
            } catch (error) {
                this.errors = error.response?.data?.errors || {};
                console.log(error.response?.data);
                throw error;
            }
        },

        async logout() {
            try {
                await api.post("/logout");
            } catch (error) {
                console.error(error);
            }

            this.user = null;
            this.errors = {};
            localStorage.removeItem("token");
            localStorage.removeItem("user");
        },

        async getUser() {
            try {
                const res = await api.get("/user");
                this.user = res.data;

                if (localStorage.getItem("token")) {
                    const cartStore = useCartStore();
                    await cartStore.fetchCart();

                    const orderStore = useOrderStore();
                    await orderStore.fetchOrders();
                }
            } catch (error) {
                this.user = null;
                localStorage.removeItem("token");
                console.error(error);
            }
        },

        restoreUser() {
            const user = localStorage.getItem("user");
            if (user) this.user = JSON.parse(user);
        },
    },
});
