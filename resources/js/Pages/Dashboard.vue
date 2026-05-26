<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Components/Sidebar.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

// Search input (can be extended to actually search investments)
const search = ref('');

// Placeholder balance/value — replace with real data from backend when available
const balance = ref(12500.75);

// Sample investments list (frontend-only placeholder)
const investments = ref([
    { id: 1, name: 'Ações Brasil', value: 5000 },
    { id: 2, name: 'Fundo Imobiliário XP', value: 3500 },
    { id: 3, name: 'Tesouro Selic', value: 3000.75 },
]);

const filteredInvestments = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return investments.value;
    return investments.value.filter((i) =>
        i.name.toLowerCase().includes(q) ||
        (user.value.name && user.value.name.toLowerCase().includes(q))
    );
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Sidebar (separated component) -->
                    <aside class="md:col-span-1">
                        <Sidebar v-model="search" :user="user" />
                    </aside>

                    <!-- Main content -->
                    <main class="md:col-span-3 space-y-6">
                        <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Olá,</p>
                                <h1 class="text-2xl font-bold text-gray-800">{{ user.name || 'Usuário' }}</h1>
                            </div>

                            <div class="text-right">
                                <p class="text-sm text-gray-500">Saldo total</p>
                                <p class="text-2xl font-semibold text-green-600">R$ {{ new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(balance) }}</p>
                            </div>
                        </div>

                        <!-- Investments quick list -->
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Seus investimentos</h2>
                            <ul class="space-y-3">
                                <li v-for="inv in filteredInvestments" :key="inv.id" class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-800">{{ inv.name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ inv.id }}</div>
                                    </div>
                                    <div class="text-gray-700 font-medium">R$ {{ new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2 }).format(inv.value) }}</div>
                                </li>
                            </ul>
                        </div>

                        <!-- News section -->
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">Notícias de investimentos</h2>
                                <a href="#" class="text-sm text-blue-600">Ver todas</a>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <article class="p-4 border rounded">
                                    <h3 class="font-medium text-gray-800">Mercado sobe com expectativa de juros</h3>
                                    <p class="text-sm text-gray-600 mt-2">Analistas apontam que a possível redução na taxa de juros pode aquecer investimentos em renda variável.</p>
                                </article>

                                <article class="p-4 border rounded">
                                    <h3 class="font-medium text-gray-800">Fundo imobiliário anuncia novos resultados</h3>
                                    <p class="text-sm text-gray-600 mt-2">Distribuições mensais e perspectivas de crescimento com novos ativos no portfólio.</p>
                                </article>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
