<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-4 overflow-x-hidden">
        <!-- Elementos decorativos de fundo -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Container principal -->
        <div class="w-full max-w-sm relative z-10">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <Link href="/" class="transform hover:scale-110 transition-transform duration-300">
                    <ApplicationLogo class="h-16 w-16 fill-current text-white drop-shadow-lg" />
                </Link>
            </div>

            <!-- Card de autenticação -->
            <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
                <!-- Gradiente de topo -->
                <div class="h-1 bg-gradient-to-r from-blue-400 via-purple-400 to-cyan-400"></div>

                <!-- Conteúdo -->
                <div class="p-8 md:p-10">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
