import { defineStore } from "pinia";
import api from "@/lib/axios";
import { useAuthStore } from "@/stores/auth";

export const useOrderStore = defineStore("order", {
    state: () => ({
        orders: [],
        loading: false,
        error: null,
        success: false,
    }),

    getters: {
        activeUserOrders: (state) => {
            const auth = useAuthStore();
            if (!auth.user || auth.isAdmin) return [];
            return state.orders.filter(
                (o) => (o.status === "pending" || o.status === "preparing" || o.status === "out_for_delivery" || o.status === "ready_for_pickup") && o.user_id === auth.user.id
            );
        }
    },

    actions: {
        async createOrder(payload) {
            this.loading = true;
            this.error = null;
            this.success = false;

            try {
                const res = await api.post("/order", payload);
                this.success = true;
                return res.data;
            } catch (err) {
                this.error = err.response?.data || "Something went wrong";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async fetchOrders() {
            this.loading = true;
            this.error = null;

            try {
                const res = await api.get("/order");
                this.orders = res.data;
            } catch (err) {
                this.error = err.response?.data || "Failed to fetch orders";
            } finally {
                this.loading = false;
            }
        },

        async updateOrderStatus(orderId, status) {
            try {
                const res = await api.patch(`/order/${orderId}`, {
                    status,
                });

                // local update
                const order = this.orders.find(
                    (o) => Number(o.id) === Number(orderId),
                );

                if (order) {
                    order.status = status;
                }

                return res.data;
            } catch (err) {
                this.error = err.response?.data || "Failed to update order";
                throw err;
            }
        },

        addOrderRealtime(order) {
            const exists = this.orders.some(
                (o) => Number(o.id) === Number(order.id)
            );

            if (!exists) {
                this.orders.unshift(order);
            }
        },

        updateOrderRealtime(order) {
            const index = this.orders.findIndex(
                (o) => Number(o.id) === Number(order.id)
            );

            if (index !== -1) {
                this.orders.splice(index, 1, { ...order });
            }
        },
    },
});
