import { defineStore } from "pinia";
import api from "@/lib/axios";

export const useCartStore = defineStore("cart", {
    state: () => ({
        items: [],
    }),

    getters: {
        cartCount: (state) =>
            state.items.reduce((total, item) => total + item.quantity, 0),

        cartTotal: (state) =>
            state.items.reduce(
                (total, item) => total + item.product.price * item.quantity,
                0,
            ),
    },

    actions: {
        // Fetch cart from backend
        async fetchCart() {
            try {
                const res = await api.get("/cart");
                this.items = Array.isArray(res.data) ? res.data : [];
            } catch (err) {
                console.error("Fetch cart failed:", err);
                this.items = [];
            }
        },

        // Add product to cart in backend
        async addToCart(product, quantity = 1) {
            try {
                const res = await api.post("/cart", {
                    product_id: product.id,
                    quantity,
                });
                this.items = res.data; // backend returns updated cart
            } catch (err) {
                console.error("Add to cart failed:", err);
            }
        },

        // Update quantity
        async updateQuantity(product_id, quantity) {
            try {
                const res = await api.put(`/cart/${product_id}`, {
                    quantity,
                });
                this.items = res.data; // backend returns updated cart
            } catch (err) {
                console.error("Update quantity failed:", err);
            }
        },

        // Remove item
        async removeItem(product_id) {
            try {
                const res = await api.delete(`/cart/${product_id}`);
                this.items = res.data; // backend returns updated cart
            } catch (err) {
                console.error("Remove item failed:", err);
            }
        },

        // Clear entire cart
        async clearCart() {
            try {
                await api.delete("/cart");
                this.items = [];
            } catch (err) {
                console.error("Clear cart failed:", err);
            }
        },
    },
});
