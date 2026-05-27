<script setup>
import { ref, watch, nextTick } from 'vue';
import axios from 'axios';
import { usePage, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const page = usePage();

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }, 
    type: {
        type: String,
        default: 'asset'
    }
});

const form = useForm({
    name: '',
    type: props.type,
    value: '',
});


const emit = defineEmits(['close']);


const submit = async () => {
    if (!form.name || !form.type || !form.value) {
        Swal.fire('Erro', 'Por favor, preencha todos os campos.', 'error');
        return;
    }

    try {
        await form.post(route('patrimony.store'), {
            onSuccess: () => {
                Swal.fire('Sucesso', page.props.flash.success, 'success');
                form.reset();
                emit('close');
                emit('refresh');
            }
        });
    } catch (e) {
        console.error(e);
        Swal.fire('Erro', 'Ocorreu um erro ao adicionar o patrimônio.', 'error');
    }
};

watch(() => props.type, (newType) => {
    form.type = newType;
});

</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto">
        <!-- Modal Backdrop, clicks outside can close it (optional, uncomment click handler if desired) -->
        <div class="fixed inset-0" @click="emit('close')"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800">Adicionar Patrimônio</h2>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form @submit.prevent="submit" class="p-5 space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome ou Descrição</label>
                    <input id="name" v-model="form.name" type="text" placeholder="Ex: Apartamento em SP" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required />
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Patrimônio</label>
                    <select id="type" v-model="form.type" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" >
                        <option value="asset">Ativo (Bem, Investimento)</option>
                        <option value="liability">Passivo (Dívida, Financiamento)</option>
                    </select>
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Valor Estimado (R$)</label>
                    <input id="value" v-model="form.value" type="number" step="0.01" min="0" placeholder="0.00" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required />
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" @click="emit('close')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        Adicionar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>