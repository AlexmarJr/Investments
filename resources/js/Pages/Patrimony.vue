<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Components/Sidebar.vue';
import { Head, usePage } from '@inertiajs/vue3';
import AddPatrimony from '@/Components/AddPatrimony.vue';
import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3'

const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const modalType = ref('asset')

const assets = ref([]);
const liabilities = ref([]);

const totalAssets = computed(() => assets.value.reduce((sum, item) => sum + parseFloat(item.value || 0), 0));
const totalLiabilities = computed(() => liabilities.value.reduce((sum, item) => sum + parseFloat(item.value || 0), 0));
const netWorth = computed(() => totalAssets.value - totalLiabilities.value);

const isAddModalOpen = ref(false);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const fetchAssets = async () => {
    try {
        const res = await axios.get(route('patrimony.assets'));
        assets.value = res.data.assets || [];
        liabilities.value = res.data.liabilities || [];
    } catch (e) {
        console.error('Erro ao carregar patrimônio', e);
    }
};

function deletePatrimony(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('patrimony.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Excluído!', 'Item excluído!', 'success');
                    fetchAssets();
                },
                onError: () => Swal.fire('Erro', 'Erro ao excluir.', 'error'),
            })
        }
    });
}

onMounted(() => {
    fetchAssets();
});

</script>

<template>
    <Head title="Patrimônio" />

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
                        <!-- Header: Net Worth -->
                        <div class="bg-indigo-600 text-white p-6 rounded-lg shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div>
                                <p class="text-indigo-200 text-sm font-medium uppercase tracking-wider">Patrimônio Líquido Total</p>
                                <h1 class="text-4xl font-bold mt-1">{{ formatCurrency(netWorth) }}</h1>
                            </div>
                            <div class="flex gap-6 text-right">
                                <div>
                                    <p class="text-indigo-200 text-xs uppercase tracking-wider">Ativos</p>
                                    <p class="text-xl font-semibold">{{ formatCurrency(totalAssets) }}</p>
                                </div>
                                <div>
                                    <p class="text-indigo-200 text-xs uppercase tracking-wider">Passivos</p>
                                    <p class="text-xl font-semibold text-rose-300">{{ formatCurrency(totalLiabilities) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Ativos -->
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-bold text-gray-800">Meus Ativos (Bens)</h2>
                                    <button class="text-sm text-indigo-600 hover:text-indigo-800 font-medium" @click="isAddModalOpen = true; modalType = 'asset'">+ Adicionar</button>
                                </div>
                                <ul class="space-y-4">
                                    <li v-for="asset in assets" :key="asset.id" class="flex justify-between items-center pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ asset.name }}</p>
                                            <p class="text-xs text-gray-500">{{ asset.type ? 'Ativo' : asset.type }}</p>
                                        </div>
                                        <div class="text-gray-900 font-semibold">{{ formatCurrency(asset.value) }}</div>
                                        <button @click="deletePatrimony(asset.id)" class="text-red-500 hover:text-red-700">
                                            <font-awesome-icon icon="trash" />
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <!-- Passivos -->
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-bold text-gray-800">Meus Passivos (Dívidas)</h2>
                                    <button class="text-sm text-indigo-600 hover:text-indigo-800 font-medium" @click="isAddModalOpen = true; modalType = 'liability'">
                                        + Adicionar
                                    </button>
                                </div>
                                <ul class="space-y-4">
                                    <li v-for="liability in liabilities" :key="liability.id" class="flex justify-between items-center pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ liability.name }}</p>
                                            <p class="text-xs text-gray-500">{{ liability.type ? 'Despesas' : liability.type }}</p>
                                        </div>
                                        <div class="text-rose-600 font-semibold">- {{ formatCurrency(liability.value) }}</div>
                                        <button @click="deletePatrimony(liability.id)" class="text-red-500 hover:text-red-700">
                                            <font-awesome-icon icon="trash" />
                                        </button>
                                    </li>
                                    <li v-if="liabilities.length === 0" class="text-sm text-gray-500">
                                        Nenhum passivo registrado.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
        <AddPatrimony 
            :show="isAddModalOpen" 
            :type="modalType"
            @close="isAddModalOpen = false"
            @refresh="fetchAssets()"
        />
    </AuthenticatedLayout>
</template>
