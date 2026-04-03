<script setup>
import { onMounted, onUnmounted } from "vue";

onMounted(() => {
    document.body.classList.add("overflow-hidden");
});

onUnmounted(() => {
    document.body.classList.remove("overflow-hidden");
});
</script>

<template>
    <div class="loader-wrapper overflow-hidden">
        <img
            src="/assets/pizza_loader.png"
            class="loader-image"
            alt="Loading"
        />
        <p class="loader-text">Preparing your cravings...</p>
    </div>
</template>

<style>
.loader-wrapper {
    position: fixed;
    inset: 0;
    background: radial-gradient(circle at center, #141414, #0b0b0b);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loader-image {
    width: 70px;
    height: 70px;
    animation: smoothSpin 1.5s linear infinite;
    transform-origin: center;
}

.loader-text {
    margin-top: 14px;
    font-size: 0.85rem;
    letter-spacing: 0.08em;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
}

@keyframes smoothSpin {
    0% {
        transform: rotate(0deg) scale(1);
    }
    50% {
        transform: rotate(180deg) scale(1);
    }
    100% {
        transform: rotate(360deg) scale(1);
    }
}
</style>
