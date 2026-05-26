<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Crie sua conta" />

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white mb-2">Criar uma conta</h1>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Nome -->
            <div>
                <InputLabel for="name" value="Nome Completo" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-2"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Seu nome completo"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
                <p class="text-xs text-gray-400 mt-1">Mínimo 3 caracteres</p>
            </div>

            <!-- Confirmar Senha -->
            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Senha"
                />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-2"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <!-- Botão de registro -->
            <PrimaryButton
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
                class="mt-8 w-full"
            >
                <span v-if="!form.processing">Criar Conta</span>
                <span v-else class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Criando conta...
                </span>
            </PrimaryButton>

            <!-- Link para login -->
            <div class="text-center pt-4 border-t border-white/10">
                <p class="text-gray-400 text-sm">
                    Já tem uma conta?
                    <Link
                        :href="route('login')"
                        class="text-blue-400 hover:text-blue-300 font-bold transition-colors"
                    >
                        Entrar aqui
                    </Link>
                </p>
            </div>

            <!-- Termos de serviço -->
            <p class="text-xs text-gray-500 text-center mt-4">
                Ao se registrar, você concorda com nossos
                <a href="#" class="text-blue-400 hover:text-blue-300">Termos de Serviço</a>
                e
                <a href="#" class="text-blue-400 hover:text-blue-300">Política de Privacidade</a>
            </p>
        </form>
    </GuestLayout>
</template>
