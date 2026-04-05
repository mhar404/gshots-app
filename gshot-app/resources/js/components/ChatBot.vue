<script setup>
import { ref, nextTick, onMounted } from "vue";
import api from "@/lib/axios";

const isOpen = ref(false);
const message = ref("");
const messages = ref([
    {
        role: "assistant",
        text: "Hello! I am your Gshot Assistant. 🧋 How can I help you today? I can recommend drinks or answer questions about our menu!",
    },
]);
const isLoading = ref(false);
const chatContainer = ref(null);

const toggleChat = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        scrollToBottom();
    }
};

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

const sendMessage = async () => {
    if (!message.value.trim() || isLoading.value) return;

    const userText = message.value;
    messages.value.push({ role: "user", text: userText });
    message.value = "";
    isLoading.value = true;

    await scrollToBottom();

    try {
        const response = await api.post("/chat", { message: userText });
        messages.value.push({ role: "assistant", text: response.data.message });
    } catch (error) {
        console.error("Chat error:", error);
        messages.value.push({
            role: "assistant",
            text: "Sorry, I am having trouble connecting right now. Please try again later. 🍵",
        });
    } finally {
        isLoading.value = false;
        await scrollToBottom();
    }
};

const quickReply = (text) => {
    message.value = text;
    sendMessage();
};
</script>

<template>
    <div class="fixed bottom-4 right-4 sm:bottom-8 sm:right-8 z-9999 font-sans">
        <!-- Chat Button -->
        <button
            @click="toggleChat"
            class="w-14 h-14 rounded-full bg-red-600 text-white border-2 border-white/10 cursor-pointer flex items-center justify-center transition-all duration-400 hover:scale-110 hover:rotate-12 shadow-[0_10px_25px_-5px_rgba(225,29,72,0.4)] hover:shadow-[0_15px_30px_-5px_rgba(225,29,72,0.6)]"
            :class="{ 'bg-zinc-900 rotate-90': isOpen }"
        >
            <i v-if="!isOpen" class="pi pi-comments text-xl sm:text-2xl"></i>
            <i v-else class="pi pi-times text-xl sm:text-2xl"></i>
        </button>

        <!-- Chat Window -->
        <transition name="slide-up">
            <div
                v-if="isOpen"
                class="fixed inset-x-4 bottom-24 sm:absolute sm:inset-auto sm:bottom-[85px] sm:right-0 w-auto sm:w-[400px] h-[70vh] sm:h-[500px] bg-zinc-900/90 border border-zinc-800 rounded-[24px] sm:rounded-[28px] flex flex-col overflow-hidden shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]"
            >
                <!-- Header -->
                <div class="p-4 sm:p-6 bg-zinc-900/90 border border-zinc-800">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div
                            class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-emerald-500 rounded-full shadow-[0_0_12px_#10b981] animate-pulse"
                        ></div>
                        <div>
                            <h3
                                class="text-base sm:text-lg font-bold text-zinc-50 m-0 tracking-tight"
                            >
                                Gshot Assistant
                            </h3>
                            <p
                                class="text-[0.75rem] sm:text-[0.85rem] text-zinc-400 m-0 mt-0.5"
                            >
                                Online | Powered by AI
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div
                    ref="chatContainer"
                    class="flex-1 p-4 sm:p-6 overflow-y-auto flex flex-col gap-4 sm:gap-5 bg-zinc-900/90 scroll-smooth custom-scrollbar"
                >
                    <div
                        v-for="(msg, index) in messages"
                        :key="index"
                        :class="[
                            'max-w-[90%] sm:max-w-[85%] p-2  rounded-[18px] sm:rounded-[20px] text-[0.9rem] sm:text-[0.95rem] leading-relaxed relative animate-fade-in',
                            msg.role === 'user'
                                ? 'self-end bg-red-600 text-white border border-white/10 rounded-br-none shadow-[0_4px_15px_-3px_rgba(225,29,72,0.3)]'
                                : 'self-start bg-zinc-900/90 border border-white/10 text-zinc-100 rounded-bl-none ',
                        ]"
                    >
                        {{ msg.text }}
                    </div>

                    <!-- Typing Indicator -->
                    <div
                        v-if="isLoading"
                        class="self-start bg-zinc-900/90 border border-zinc-800 text-zinc-100 p-3.5 sm:p-4 rounded-[18px] sm:rounded-[20px] rounded-bl-none flex gap-1 items-center"
                    >
                        <span
                            class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce"
                        ></span>
                        <span
                            class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce [animation-delay:0.2s]"
                        ></span>
                        <span
                            class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce [animation-delay:0.4s]"
                        ></span>
                    </div>
                </div>

                <!-- Quick Replies -->
                <div
                    v-if="messages.length === 1 && !isLoading"
                    class="px-4 sm:px-5 pb-4 flex flex-wrap gap-2 bg-zinc-900/90"
                >
                    <button
                        @click="quickReply('Show me the menu')"
                        class="bg-white/5 border border-rose-500/30 text-rose-400 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-[0.8rem] sm:text-[0.85rem] cursor-pointer transition-all hover:bg-rose-500/10 hover:border-rose-500 active:scale-95"
                    >
                        Show menu
                    </button>
                    <button
                        @click="quickReply('What is the best seller?')"
                        class="bg-white/5 border border-rose-500/30 text-rose-400 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-[0.8rem] sm:text-[0.85rem] cursor-pointer transition-all hover:bg-rose-500/10 hover:border-rose-500 active:scale-95"
                    >
                        Best sellers
                    </button>
                    <button
                        @click="quickReply('Do you have Milk Tea?')"
                        class="bg-white/5 border border-rose-500/30 text-rose-400 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-[0.8rem] sm:text-[0.85rem] cursor-pointer transition-all hover:bg-rose-500/10 hover:border-rose-500 active:scale-95"
                    >
                        Milk Tea
                    </button>
                </div>

                <!-- Input Area -->
                <form
                    @submit.prevent="sendMessage"
                    class="p-4 sm:p-5 bg-zinc-900/90 border border-t border-zinc-800 flex gap-2 sm:gap-3"
                >
                    <input
                        v-model="message"
                        type="text"
                        placeholder="Type a message..."
                        :disabled="isLoading"
                        class="flex-1 px-4 py-2.5 sm:px-5 sm:py-3 bg-zinc-900/90 border border-zinc-800 text-white rounded-xl outline-none transition-all focus:border-rose-600 focus:bg-zinc-800/80 disabled:opacity-50 text-[16px]"
                    />
                    <!-- text-[16px] prevents iOS auto-zoom on input focus -->
                    <button
                        type="submit"
                        :disabled="isLoading || !message.trim()"
                        class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-rose-600 text-white flex items-center justify-center transition-all active:scale-90 hover:bg-rose-500 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <i class="pi pi-send"></i>
                    </button>
                </form>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: tranzincY(10px);
    }
    to {
        opacity: 1;
        transform: tranzincY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out forwards;
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: tranzincY(40px) scale(0.9);
}

@media (max-width: 480px) {
    .chatbot-wrapper {
        right: 1rem;
        bottom: 1rem;
    }
}
</style>
