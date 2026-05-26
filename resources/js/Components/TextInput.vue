<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full px-4 py-3 rounded-lg border border-white/10 bg-white/5 text-white placeholder-gray-400 transition-colors duration-200 focus:outline-none focus:border-blue-400 focus:bg-white/10 hover:border-white/20"
        v-model="model"
        ref="input"
        @focus="isFocused = true"
        @blur="isFocused = false"
    />
</template>
