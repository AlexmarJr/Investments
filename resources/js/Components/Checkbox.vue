<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="w-5 h-5 rounded border-white/20 bg-white/5 text-blue-500 accent-blue-500 cursor-pointer transition-all duration-200 focus:ring-2 focus:ring-blue-400 focus:ring-offset-0 hover:border-blue-400"
    />
</template>
