<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm font-semibold text-gray-200 mb-2 tracking-wide">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
