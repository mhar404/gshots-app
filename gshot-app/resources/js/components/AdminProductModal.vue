<script setup>
import { ref } from "vue";
import { useProductStore } from "@/stores/product";

const productStore = useProductStore();

const isAdminModalOpen = ref(false);
const adminSelectedItem = ref({
    name: "",
    description: "",
    price: "",
    image: "",
    category: "",
});
const previewImage = ref(null);
const selectedFile = ref(null);

const open = (item) => {
    if (item) {
        // copy object to avoid mutating directly
        adminSelectedItem.value = { ...item };
        if (typeof adminSelectedItem.value.is_available === "undefined") {
            adminSelectedItem.value.is_available = true;
        }
    } else {
        adminSelectedItem.value = {
            name: "",
            description: "",
            price: "",
            image: "",
            category: "",
            is_available: true,
        };
    }
    previewImage.value = null;
    selectedFile.value = null;
    isAdminModalOpen.value = true;
};

const closeAdminModal = () => {
    isAdminModalOpen.value = false;
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        selectedFile.value = file;
        previewImage.value = URL.createObjectURL(file);
    }
};

const saveAdminEdit = async () => {
    const formData = new FormData();
    try {
        formData.append("name", adminSelectedItem.value.name);
        formData.append("description", adminSelectedItem.value.description);
        formData.append("price", adminSelectedItem.value.price);
        formData.append("category", adminSelectedItem.value.category);
        formData.append(
            "is_available",
            adminSelectedItem.value.is_available ? 1 : 0,
        );

        if (selectedFile.value) {
            formData.append("image", selectedFile.value);
        }

        if (adminSelectedItem.value.id) {
            await productStore.updateProduct(
                adminSelectedItem.value.id,
                formData,
            );
        } else {
            await productStore.createProduct(formData);
        }

        closeAdminModal();
    } catch (err) {
        console.error("Save failed:", err);
    }
};

const confirmDelete = () => {
    if (confirm("Are you sure you want to delete this product?")) {
        productStore.deleteProduct(adminSelectedItem.value.id);
        closeAdminModal();
    }
};

defineExpose({ open });
</script>

<template>
    <!-- admin modal -->
    <div
        v-if="isAdminModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center px-3 sm:px-6"
    >
        <!-- Overlay -->
        <div
            class="absolute inset-0 bg-black/70"
            @click="closeAdminModal"
        ></div>

        <!-- Modal Box -->
        <div
            class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto bg-black/20 backdrop-blur-sm border border-white/20 rounded-xl p-4 sm:p-6 md:p-8 shadow-2xl animate-fade-in flex flex-col md:flex-row gap-4 sm:gap-6 text-white"
        >
            <!-- Close -->
            <button
                @click="closeAdminModal"
                class="absolute top-2 right-3 text-gray-300 hover:text-white text-xl cursor-pointer z-10"
            >
                ✕
            </button>

            <!-- Image Preview + Upload -->
            <div class="flex-1 flex flex-col">
                <label class="text-gray-300 mb-1 text-sm sm:text-base"
                    >Image</label
                >

                <div
                    v-if="previewImage || adminSelectedItem.image_url"
                    class="w-full h-40 sm:h-48 md:h-56 rounded-xl mb-2 border border-white/20 overflow-hidden bg-black/40"
                >
                    <img
                        :src="previewImage || adminSelectedItem.image_url"
                        alt="Product Image"
                        class="w-full h-full object-cover text-gray-300 text-center"
                    />
                </div>
                <div
                    v-else
                    class="w-full h-40 sm:h-48 md:h-56 rounded-xl mb-2 border border-white/20 bg-black/40 flex items-center justify-center text-gray-500"
                >
                    No image provided
                </div>

                <input
                    type="file"
                    accept="image/*"
                    @change="handleImageUpload"
                    class="w-full text-xs sm:text-sm text-white file:bg-red-600 file:text-white file:px-3 file:py-1.5 sm:file:px-4 sm:file:py-2 file:rounded-full file:border-0 cursor-pointer file:cursor-pointer mt-2"
                />
            </div>

            <!-- Form Fields -->
            <div class="flex-1 flex flex-col justify-between">
                <div>
                    <label class="text-gray-300 text-sm sm:text-base"
                        >Name</label
                    >
                    <input
                        v-model="adminSelectedItem.name"
                        class="w-full mb-3 sm:mb-4 px-3 py-2 rounded bg-white/10 text-white border border-white/10 focus:border-red-500 outline-none transition text-sm sm:text-base"
                    />

                    <!-- We only restrict category changes for new products, or we can allow it anywhere. Original code restricted it for edit? Let's leave it as original -->
                    <div v-if="!adminSelectedItem.id">
                        <label class="text-gray-300 text-sm sm:text-base"
                            >Category</label
                        >
                        <select
                            v-model="adminSelectedItem.category"
                            class="w-full mb-3 px-3 py-2 rounded bg-black/60 text-white border border-white/20 focus:border-red-500 outline-none transition"
                        >
                            <option
                                value=""
                                disabled
                                class="bg-black text-white"
                            >
                                Select Category
                            </option>
                            <option class="bg-black text-white">Drinks</option>
                            <option class="bg-black text-white">Burgers</option>
                            <option class="bg-black text-white">Pizzas</option>
                            <option class="bg-black text-white">
                                RiceMeals
                            </option>
                        </select>
                    </div>

                    <label class="text-gray-300 text-sm sm:text-base"
                        >Description</label
                    >
                    <textarea
                        v-model="adminSelectedItem.description"
                        rows="3"
                        class="w-full mb-3 sm:mb-4 px-3 py-2 rounded bg-white/10 text-white border border-white/10 focus:border-red-500 outline-none resize-none text-sm sm:text-base transition"
                    ></textarea>

                    <label class="text-gray-300 text-sm sm:text-base"
                        >Price</label
                    >
                    <input
                        v-model="adminSelectedItem.price"
                        type="number"
                        class="w-full mb-3 sm:mb-4 px-3 py-2 rounded bg-white/10 text-white border border-white/10 focus:border-red-500 outline-none text-sm sm:text-base transition"
                    />

                    <div
                        class="flex items-center justify-between mb-3 sm:mb-4 bg-white/5 p-3 rounded border border-white/10"
                    >
                        <label
                            class="text-gray-300 text-sm sm:text-base select-none"
                            >Available</label
                        >
                        <label
                            class="relative inline-flex items-center cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                v-model="adminSelectedItem.is_available"
                                class="sr-only peer"
                            />
                            <div
                                class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"
                            ></div>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <!-- Delete -->
                    <button
                        v-if="adminSelectedItem.id"
                        @click="confirmDelete"
                        class="flex-1 flex items-center justify-center px-5 py-2.5 rounded-xl bg-red-500/20 text-red-400 hover:bg-red-500 hover:text-white transition cursor-pointer"
                    >
                        <i class="pi pi-trash text-lg"></i>
                    </button>

                    <!-- Save -->
                    <button
                        @click="saveAdminEdit"
                        class="flex-3 flex items-center justify-center px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold transition cursor-pointer text-sm sm:text-base shadow-lg"
                    >
                        {{
                            adminSelectedItem.id
                                ? "Save Changes"
                                : "Add Product"
                        }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
