<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message" class="animate-shake">
        <p class="text-sm font-semibold text-red-400 bg-red-500/10 border border-red-500/30 rounded-lg px-3 py-2">
            {{ message }}
        </p>
    </div>
</template>

<style scoped>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}
</style>
