<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Components/Sidebar.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

const investments = ref([
    { id: 1, name: 'Ações Brasil', type: 'Ações', value: 5000, allocation: '40%' },
    { id: 2, name: 'Fundo Imobiliário XP', type: 'FII', value: 3500, allocation: '28%' },
    { id: 3, name: 'Tesouro Selic', type: 'Renda Fixa', value: 3000.75, allocation: '24%' },
]);

const total = computed(() => investments.value.reduce((s, i) => s + (i.value || 0), 0));

const formatCurrency = (v) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v);

onMounted(() => {
    // placeholder: later we can fetch real investments from backend
});
</script>

<template>
    <Head title="Investimentos" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Sidebar -->
                    <aside class="md:col-span-1">
                        <Sidebar :user="user" />
                    </aside>

                    <!-- Main content -->
                    <main class="md:col-span-3 space-y-6">
                        <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Visão geral</p>
                                <h1 class="text-2xl font-bold text-gray-800">Seus Investimentos</h1>
                            </div>

                            <div class="text-right">
                                <p class="text-sm text-gray-500">Saldo total</p>
                                <p class="text-2xl font-semibold text-green-600">{{ formatCurrency(total) }}</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Carteira</h2>
                            <ul class="space-y-3">
                                <li v-for="inv in investments" :key="inv.id" class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-800">{{ inv.name }}</div>
                                        <div class="text-sm text-gray-500">{{ inv.type }} • {{ inv.allocation }}</div>
                                    </div>
                                    <div class="text-gray-700 font-medium">{{ formatCurrency(inv.value) }}</div>
                                </li>
                            </ul>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
