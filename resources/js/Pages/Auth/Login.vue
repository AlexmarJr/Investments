<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <!-- Mensagem de sucesso -->
        <div v-if="status" class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-200 text-sm font-semibold animate-pulse">
            {{ status }}
        </div>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Bem-vindo de volta</h1>
            <p class="text-gray-300">Faça login para acessar sua conta</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="seu@email.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Senha -->
            <div>
                <InputLabel for="password" value="Senha" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-2"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Lembrar-me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-3 text-sm text-gray-300 group-hover:text-white transition-colors">
                        Lembrar-me
                    </span>
                </label>

                <!-- Esqueceu a senha -->
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors font-semibold"
                >
                    Esqueceu a senha?
                </Link>
            </div>

            <!-- Botão de login -->
            <PrimaryButton
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
                class="mt-8 w-full"
            >
                <span v-if="!form.processing">Entrar</span>
                <span v-else class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Entrando...
                </span>
            </PrimaryButton>

            <!-- Link para registro -->
            <div class="text-center pt-4 border-t border-white/10">
                <p class="text-gray-400 text-sm">
                    Não tem conta?
                    <Link
                        :href="route('register')"
                        class="text-blue-400 hover:text-blue-300 font-bold transition-colors"
                    >
                        Criar uma agora
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
