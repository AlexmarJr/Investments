<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
});

const isCollapsed = ref(false);
const page = usePage();

const menuItems = [
    {
        name: 'Dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M13 5v6h6',
        route: 'dashboard',
    },
    {
        name: 'Investimentos',
        icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        route: 'investments',
    },
    {
        name: 'Patrimônio',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        route: '#', // placeholder
    },
    {
        name: 'Relatórios',
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        route: '#', // placeholder
    },
];

const initials = computed(() => {
    const user = props.user?.name ? props.user : page.props.auth.user;
    if (!user || !user.name) return 'U';
    return user.name.split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase();
});

const currentUser = computed(() => props.user?.name ? props.user : page.props.auth.user);
</script>

<template>
        <aside
            :class="[isCollapsed ? 'w-20' : 'w-full']"
            class="flex flex-col h-auto min-w-0 bg-white text-slate-700 transition-all duration-300 ease-in-out border-r border-slate-200 overflow-x-hidden"
        >
        <!-- Header / Logo -->
        <div class="flex items-center h-20 px-6 shrink-0">
            <div v-if="!isCollapsed" class="text-xl font-bold tracking-tight text-indigo-600 truncate max-w-full">
                Invest<span class="text-indigo-400">Flow</span>
            </div>
        </div>

        <!-- Navigation -->
          <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto custom-scrollbar min-w-0">
              <div v-for="item in menuItems" :key="item.name" class="min-w-0 pr-2">
                <Link
                    :href="item.route.startsWith('#') ? '#' : route(item.route)"
                    :class="[
                        route().current(item.route)
                            ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
                            : 'hover:bg-slate-100 hover:text-slate-900 border-transparent',
                        'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 border group'
                    ]"
                >
                    <svg
                        :class="[
                            route().current(item.route) ? 'text-indigo-700' : 'text-slate-400 group-hover:text-slate-600',
                            'w-6 h-6 shrink-0 transition-colors'
                        ]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="item.icon" />
                    </svg>
                    <span v-if="!isCollapsed" class="font-medium truncate">{{ item.name }}</span>
                </Link>
            </div>

            <!-- Separator -->
            <div class="pt-4 pb-2">
                <div class="border-t border-slate-200 mx-2"></div>
            </div>

            <Link
                :href="route('profile.edit')"
                :class="[
                    route().current('profile.edit')
                            ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
                            : 'hover:bg-slate-100 hover:text-slate-900 border-transparent',
                    'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 border group'
                ]"
            >
                <svg
                    :class="[
                        route().current('profile.edit') ? 'text-indigo-700' : 'text-slate-400 group-hover:text-slate-600',
                        'w-6 h-6 shrink-0 transition-colors'
                    ]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span v-if="!isCollapsed" class="font-medium truncate">Perfil</span>
            </Link>
        </nav>

        <!-- Footer / User Profile -->
        <div class="p-4 mt-auto border-t border-slate-200">
            <div class="flex items-center gap-3 mb-4" v-if="!isCollapsed">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 border border-slate-200 text-indigo-600 font-bold shrink-0">
                    {{ initials }}
                </div>
                <div class="min-w-0 overflow-hidden">
                    <p class="text-sm font-semibold text-slate-900 truncate">{{ currentUser.name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ currentUser.email }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center gap-3 px-3 py-2 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-all duration-200 group w-full text-left"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 group-hover:stroke-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span v-if="!isCollapsed" class="text-sm font-medium">Sair</span>
                </Link>

                <button
                    @click="isCollapsed = !isCollapsed"
                    class="flex items-center gap-3 px-3 py-2 mt-2 text-slate-500 hover:text-slate-900 rounded-lg transition-all duration-200 group w-full"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 transition-transform duration-300 shrink-0"
                        :class="{ 'rotate-180': isCollapsed }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <span v-if="!isCollapsed" class="text-sm font-medium">Recolher</span>
                </button>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>

