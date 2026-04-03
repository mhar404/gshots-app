<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";
import { useUiStore } from "../stores/ui";
import { useRouter } from "vue-router";

const emit = defineEmits(["login-success"]);

const ui = useUiStore();
const auth = useAuthStore();
const router = useRouter();

const form = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const isSubmitting = ref(false);

const resetForm = () => {
    form.value = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    };
    auth.clearErrors();
};

const handleAuth = async () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    auth.clearErrors();

    try {
        if (ui.authMode === "login") {
            await auth.login({
                email: form.value.email,
                password: form.value.password,
            });
            ui.showAuthToast("Welcome back!");
            if (auth.isAdmin) {
                router.push("/admin");
            }
        } else {
            await auth.register(form.value);
            ui.showAuthToast("Registration successful!");
        }
        ui.closeAuthModal();
        resetForm();
        emit("login-success");
    } catch (err) {
        console.error("Auth Exception:", err);
    } finally {
        isSubmitting.value = false;
    }
};

const toggleMode = () => {
    ui.setAuthMode(ui.authMode === "login" ? "register" : "login");
    resetForm();
};
</script>

<template>
    <div
        v-if="ui.isAuthOpen"
        class="fixed inset-0 z-100 flex items-center justify-center px-4"
    >
        <!-- Overlay -->
        <div
            class="absolute inset-0 bg-black/70 backdrop-blur-sm"
            @click="ui.closeAuthModal()"
        ></div>

        <!-- Modal Box -->
        <div
            class="relative w-full max-w-md bg-black/20 backdrop-blur-md border border-white/20 rounded-2xl p-6 sm:p-8 shadow-2xl animate-fade-in"
        >
            <!-- Close Button -->
            <button
                @click="ui.closeAuthModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-white transition cursor-pointer"
            >
                ✕
            </button>

            <!-- Header -->
            <h2 class="text-2xl font-bold text-center mb-6 text-white">
                {{ ui.authMode === "login" ? "Sign In" : "Register" }}
            </h2>

            <!-- FORM -->
            <form @submit.prevent="handleAuth" class="space-y-4">
                <!-- Name Field (Register Only) -->
                <div v-if="ui.authMode === 'register'">
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        Name
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500 transition"
                        placeholder="John Doe"
                    />
                    <p
                        v-if="auth.errors.name"
                        class="text-red-400 text-xs mt-1"
                    >
                        {{ auth.errors.name[0] }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        Email Address
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500 transition"
                        placeholder="you@example.com"
                    />
                    <p
                        v-if="auth.errors.email"
                        class="text-red-400 text-xs mt-1"
                    >
                        {{ auth.errors.email[0] }}
                    </p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        Password
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500 transition"
                        placeholder="••••••••"
                    />
                    <p
                        v-if="auth.errors.password"
                        class="text-red-400 text-xs mt-1"
                    >
                        {{ auth.errors.password[0] }}
                    </p>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg font-semibold transition mt-6 cursor-pointer"
                >
                    <span v-if="isSubmitting">Please wait...</span>
                    <span v-else>
                        {{ ui.authMode === "login" ? "Sign In" : "Register" }}
                    </span>
                </button>
            </form>

            <!-- Toggle Mode -->
            <div class="mt-6 text-center text-sm text-gray-400">
                <p v-if="ui.authMode === 'login'">
                    Don't have an account?
                    <button
                        type="button"
                        @click="toggleMode"
                        class="text-red-400 hover:text-red-300 font-semibold cursor-pointer"
                    >
                        Register here
                    </button>
                </p>
                <p v-else>
                    Already have an account?
                    <button
                        type="button"
                        @click="toggleMode"
                        class="text-red-400 hover:text-red-300 font-semibold cursor-pointer"
                    >
                        Sign in here
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>
