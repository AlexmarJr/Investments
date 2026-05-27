<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Components/Sidebar.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

// Placeholder balance/value — replace with real data from backend when available
const balance = ref(12500.75);

// Sample investments list (frontend-only placeholder)
const investments = ref([
    { id: 1, name: 'Ações Brasil', value: 5000 },
    { id: 2, name: 'Fundo Imobiliário XP', value: 3500 },
    { id: 3, name: 'Tesouro Selic', value: 3000.75 },
]);

const newsItems = ref([]);

const fetchNews = async () => {
    try {
        const res = await axios.get(route('news.investments'));
        newsItems.value = res.data.items || [];
    } catch (e) {
        console.error('Failed to load news', e);
        newsItems.value = [];
    }
};

const formatDate = (d) => {
    try {
        const dt = new Date(d);
        return isNaN(dt.getTime()) ? '' : dt.toLocaleString('pt-BR');
    } catch (e) {
        return '';
    }
};

onMounted(() => {
    fetchNews();
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
                        <Sidebar :user="user" />
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
                                <li v-for="inv in investments" :key="inv.id" class="flex justify-between items-center">
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
                                <a href="https://www.infomoney.com.br/" target="_blank" class="text-sm text-blue-600">Ver todas</a>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <article v-if="newsItems.length === 0" class="p-4 border rounded">
                                    <p class="text-sm text-gray-600">Carregando notícias...</p>
                                </article>

                                <article v-for="item in newsItems" :key="item.link" class="p-4 border rounded">
                                    <a :href="item.link" target="_blank" rel="noopener noreferrer" class="text-lg font-medium text-gray-800 hover:underline">{{ item.title }}</a>
                                    <p class="text-sm text-gray-600 mt-2">{{ item.snippet }}</p>
                                    <p class="text-xs text-gray-400 mt-2">{{ formatDate(item.pubDate) }}</p>
                                </article>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
