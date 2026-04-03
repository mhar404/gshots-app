import { defineStore } from "pinia";
import api from "@/lib/axios";

export const useProductStore = defineStore("product", {
    state: () => ({
        products: [],
        product: null,
        loading: false,
        error: null,
    }),

    actions: {
        // Fetch all products (public)
        async fetchProducts() {
            this.loading = true;
            this.error = null;
            try {
                const res = await api.get("/products");
                this.products = res.data;
                console.log(res.data);
            } catch (err) {
                console.error("Error fetching products:", err);
                this.error = err;
            } finally {
                this.loading = false;
            }
        },

        // Fetch single product (public)
        async fetchProduct(id) {
            this.loading = true;
            this.error = null;
            try {
                const res = await api.get(`/products/${id}`);
                this.product = res.data;
            } catch (err) {
                console.error("Error fetching product:", err);
                this.error = err;
            } finally {
                this.loading = false;
            }
        },

        // Admin: create product
        async createProduct(formData) {
            this.loading = true;
            this.error = null;
            try {
                const res = await api.post("/products", formData); // handles multipart automatically
                this.products.push(res.data);
                return res.data;
            } catch (err) {
                console.error("Error creating product:", err);
                console.error("STATUS:", err.response?.status);
                console.error("DATA:", err.response?.data);
                this.error = err;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Admin: update product
        async updateProduct(id, formData) {
            this.loading = true;
            this.error = null;
            try {
                const res = await api.post(
                    `/products/${id}?_method=PUT`,
                    formData,
                );
                const index = this.products.findIndex((p) => p.id == id);
                if (index !== -1) this.products[index] = res.data;
                return res.data;
            } catch (err) {
                console.error("Error updating product:", err);
                this.error = err;
                throw err;
            } finally {
                this.loading = false;
            }
        },

        // Admin: delete product
        async deleteProduct(id) {
            this.loading = true;
            this.error = null;
            try {
                await api.delete(`/products/${id}`);
                this.products = this.products.filter((p) => p.id !== id);
            } catch (err) {
                console.error("Error deleting product:", err);
                this.error = err;
                throw err;
            } finally {
                this.loading = false;
            }
        },
    },

    // Optional: persist products in localStorage so they survive reload
    // persist: true,
});
