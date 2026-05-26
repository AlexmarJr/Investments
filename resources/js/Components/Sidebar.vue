<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    user: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['update:modelValue']);

const username = computed(() => {
    return (typeof __VUE_PROD_DEVTOOLS__ === 'undefined') ? undefined : undefined; // placeholder to keep linters happy
});

const onInput = (e) => emit('update:modelValue', e.target.value);
</script>

<template>
    <div>
        <div class="sticky top-6 space-y-6">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">{{ (user && user.name) ? user.name.charAt(0) : 'U' }}</div>
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ user.name || 'Usuário' }}</div>
                        <div class="text-xs text-gray-500">{{ user.email || '' }}</div>
                    </div>
                </div>
            </div>

            <nav class="bg-white p-4 rounded-lg shadow-sm">
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="flex items-center gap-3 text-gray-700 hover:text-blue-600">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/></svg>
                            <span class="font-medium">Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 text-gray-700 hover:text-blue-600">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6"/></svg>
                            <span class="font-medium">Investimentos</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 text-gray-700 hover:text-blue-600">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"/></svg>
                            <span class="font-medium">Relatórios</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="bg-white p-4 rounded-lg shadow-sm">
                <label class="block text-sm font-medium text-gray-700">Buscar</label>
                <input
                    :value="modelValue"
                    @input="onInput"
                    placeholder="Buscar investimentos ou usuário"
                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                />
            </div>
        </div>
    </div>
</template>
