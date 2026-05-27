<script setup>
import { ref, watch, nextTick } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const page = usePage();
const user = page.props.auth.user;

const STORAGE_KEY = 'deepseek_chat_' + (user?.id ?? 'guest');

const isOpen = ref(false);
const isModalOpen = ref(false);
const messages = ref([]);
const newMessage = ref('');
const isLoading = ref(false);
const chatWindowRef = ref(null);

const loadMessagesFromSession = () => {
    if (typeof window === 'undefined' || !window.sessionStorage) return;
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY);

        console.log(raw);
        if (raw) {
            const parsed = JSON.parse(raw);
            if (Array.isArray(parsed)) {
                // ensure ai messages have sanitized html
                messages.value = parsed.map((m) => {
                    if (m && m.sender === 'ai' && !m.html) {
                        try {
                            m.html = DOMPurify.sanitize(marked.parse(m.text || ''));
                        } catch (e) {
                            m.html = m.text || '';
                        }
                    }
                    return m;
                });
            }
        }
    } catch (e) {
        // ignore parse errors
    }
};

// load saved messages on setup
loadMessagesFromSession();

const toggleChat = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        scrollToBottom();
    }
};

const openModal = () => {
    isModalOpen.value = true;
    isOpen.value = true;
    nextTick(() => scrollToBottom());
};

const closeModal = () => {
    isModalOpen.value = false;
};

const clearChat = async () => {
    const result = await Swal.fire({
        title: 'Limpar conversa?',
        text: 'Esta ação removerá a conversa apenas desta aba.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, limpar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
    });

    if (!result.isConfirmed) return;

    messages.value = [];
    if (typeof window !== 'undefined' && window.sessionStorage) {
        sessionStorage.removeItem(STORAGE_KEY);
    }

    await Swal.fire('Limpo', 'Conversa apagada.', 'success');
};

const sendMessage = async () => {
    if (newMessage.value.trim() === '') return;

    const userMessage = { text: newMessage.value, sender: 'user' };
    messages.value.push(userMessage);
    newMessage.value = '';
    isLoading.value = true;
    scrollToBottom();

    try {
        // send recent history as context (last 12 messages)
        const historyToSend = messages.value.slice(-12).map((m) => ({ sender: m.sender, text: m.text }));

        const response = await axios.post(route('chat.chat_api'), {
            message: userMessage.text,
            userId: user.id,
            history: historyToSend,
        });
        const aiText = response.data.reply || '';
        let aiHtml = aiText;
        try {
            aiHtml = DOMPurify.sanitize(marked.parse(aiText));
        } catch (e) {
            aiHtml = aiText;
        }

        messages.value.push({ text: aiText, html: aiHtml, sender: 'ai' });
    } catch (error) {
        console.error('Error sending message:', error);
        messages.value.push({ text: 'Desculpe, houve um erro ao processar sua mensagem.', sender: 'ai' });
    } finally {
        isLoading.value = false;
        scrollToBottom();
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (chatWindowRef.value) {
            chatWindowRef.value.scrollTop = chatWindowRef.value.scrollHeight;
        }
    });
};

watch(messages, (val) => {
    // persist in sessionStorage on every change
    if (typeof window !== 'undefined' && window.sessionStorage) {
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(val));
        } catch (e) {
            // ignore storage errors
        }
    }

    scrollToBottom();
}, { deep: true });
</script>

<style scoped>
:deep(.chat-message-ai) pre {
    background-color: #0f172a;
    color: #f8fafc;
    padding: 0.5rem;
    border-radius: 0.375rem;
    overflow: auto;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, 'Roboto Mono', 'Segoe UI Mono', monospace;
    font-size: 0.875rem;
}
:deep(.chat-message-ai) code {
    background-color: #e6eef8;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    color: #0b1220;
}
:deep(.chat-message-ai) blockquote {
    border-left: 4px solid #c7d2fe;
    padding-left: 0.75rem;
    color: #111827;
    margin: 0.5rem 0;
}
:deep(.chat-message-ai) a {
    color: #3730a3;
    text-decoration: underline;
}
</style>

<template>
    <div class="fixed bottom-6 right-6 z-50">
        <!-- Chat Bubble -->
        <button
            @click="toggleChat"
            class="w-14 h-14 bg-indigo-600 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            :class="{ 'rotate-45': isOpen }"
        >
            <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.117C6.85 14.125 10.7 12 15 12h2.5c1.381 0 2.5 1.119 2.5 2.5V17m-5-6v5h-2.5V11m-6.5 0h-2.5V16h2.5V11z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Chat Window (compact) -->
        <div
            v-if="isOpen && !isModalOpen"
            class="absolute bottom-16 right-0 w-80 h-96 bg-white rounded-lg shadow-xl flex flex-col overflow-hidden"
        >
            <div class="bg-indigo-600 text-white p-4 flex items-center justify-between shadow-md">
                <div class="flex items-center gap-3">
                    <button @click="openModal" title="Maximizar" class="text-white hover:text-indigo-100 focus:outline-none">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 3C16.5523 3 17 3.44772 17 4V9.25C17 9.66421 16.6642 10 16.25 10C15.8358 10 15.5 9.66421 15.5 9.25V5.559L5.559 15.5H9.25C9.66421 15.5 10 15.8358 10 16.25C10 16.6642 9.66421 17 9.25 17H4C3.44772 17 3 16.5523 3 16V10.75C3 10.3358 3.33579 10 3.75 10C4.16421 10 4.5 10.3358 4.5 10.75V14.439L14.439 4.5H10.75C10.3358 4.5 10 4.16421 10 3.75C10 3.33579 10.3358 3 10.75 3H16Z" fill="currentColor"/>
                        </svg>
                    </button>
                    <h3 class="text-lg font-semibold">InvestFlow AI Chat</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="clearChat" title="Limpar conversa" class="text-white hover:text-red-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v1H9V4a1 1 0 011-1z" />
                        </svg>
                    </button>
                    <button @click="toggleChat" class="text-white hover:text-indigo-100 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div ref="chatWindowRef" class="flex-1 p-4 overflow-y-auto space-y-4 custom-scrollbar">
                <div v-for="(message, index) in messages" :key="index" :class="{'flex justify-end': message.sender === 'user', 'flex justify-start': message.sender === 'ai'}">
                    <div
                        :class="[
                            message.sender === 'user'
                                ? 'bg-indigo-500 text-white rounded-bl-xl rounded-tl-xl rounded-tr-xl'
                                : 'bg-gray-200 text-gray-800 rounded-br-xl rounded-tl-xl rounded-tr-xl chat-message-ai',
                        ]"
                        class="px-4 py-2 max-w-[75%] break-words whitespace-pre-wrap"
                    >
                        {{ message.text }}
                    </div>
                </div>
                <div v-if="isLoading" class="flex justify-start">
                    <div class="bg-gray-200 text-gray-600 px-4 py-2 rounded-br-xl rounded-tl-xl rounded-tr-xl animate-pulse">
                        Digitando...
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 p-4 flex items-center gap-2">
                <input
                    type="text"
                    v-model="newMessage"
                    @keyup.enter="sendMessage"
                    placeholder="Digite sua mensagem..."
                    class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    :disabled="isLoading"
                />
                <button
                    @click="sendMessage"
                    :disabled="isLoading || newMessage.trim() === ''"
                    class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Modal (expanded) -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-4xl h-[80vh] bg-white rounded-lg shadow-xl flex flex-col overflow-hidden">
                <div class="bg-indigo-600 text-white p-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">InvestFlow AI Chat</h3>
                    <div class="flex items-center gap-2">
                        <button @click="clearChat" title="Limpar conversa" class="text-white hover:text-red-200 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v1H9V4a1 1 0 011-1z" />
                            </svg>
                        </button>
                        <button @click="closeModal" class="text-white hover:text-indigo-100 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div ref="chatWindowRef" class="flex-1 p-6 overflow-y-auto space-y-4 custom-scrollbar">
                    <div v-for="(message, index) in messages" :key="index" :class="{'flex justify-end': message.sender === 'user', 'flex justify-start': message.sender === 'ai'}">
                        <div
                            :class="[
                                message.sender === 'user'
                                    ? 'bg-indigo-500 text-white rounded-bl-xl rounded-tl-xl rounded-tr-xl'
                                    : 'bg-gray-200 text-gray-800 rounded-br-xl rounded-tl-xl rounded-tr-xl chat-message-ai',
                            ]"
                            class="px-4 py-2 max-w-[75%]"
                        >
                            <template v-if="message.sender === 'ai'">
                                <div v-html="message.html || message.text"></div>
                            </template>
                            <template v-else>
                                {{ message.text }}
                            </template>
                        </div>
                    </div>
                    <div v-if="isLoading" class="flex justify-start">
                        <div class="bg-gray-200 text-gray-600 px-4 py-2 rounded-br-xl rounded-tl-xl rounded-tr-xl animate-pulse">
                            Digitando...
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 p-4 flex items-center gap-2">
                    <input
                        type="text"
                        v-model="newMessage"
                        @keyup.enter="sendMessage"
                        placeholder="Digite sua mensagem..."
                        class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        :disabled="isLoading"
                    />
                    <button
                        @click="sendMessage"
                        :disabled="isLoading || newMessage.trim() === ''"
                        class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
