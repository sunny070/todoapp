<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
});

const statCards = [
    { name: 'Total Tasks', value: props.stats.total, icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', color: 'indigo' },
    { name: 'Completed', value: props.stats.completed, icon: 'M5 13l4 4L19 7', color: 'emerald' },
    { name: 'High Priority', value: props.stats.high_priority, icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', color: 'rose' },
    { name: 'Overdue', value: props.stats.overdue, icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', color: 'amber' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400 transition-colors duration-300">
                Dashboard Overview
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-[calc(100vh-64px)] transition-colors duration-300">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="mb-12 overflow-hidden bg-white dark:bg-gray-800 shadow-xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700 relative transition-colors duration-300">
                    <div class="absolute top-0 right-0 -mt-20 -mr-20 h-80 w-80 bg-indigo-50 dark:bg-indigo-900/10 rounded-full opacity-50 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-80 w-80 bg-purple-50 dark:bg-purple-900/10 rounded-full opacity-50 blur-3xl"></div>
                    
                    <div class="p-10 md:p-16 relative z-10">
                        <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
                            <div class="flex-1 text-center lg:text-left">
                                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-sm font-bold mb-6">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                                    </span>
                                    Productivity Session Active
                                </div>
                                <h1 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6 tracking-tight">
                                    Hey <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">{{ $page.props.auth.user.name }}</span>!
                                </h1>
                                <p class="text-xl text-gray-500 dark:text-gray-400 mb-10 max-w-2xl leading-relaxed">
                                    You have <span class="font-bold text-gray-900 dark:text-white">{{ stats.total - stats.completed }} tasks</span> waiting for you. 
                                    <span v-if="stats.overdue > 0" class="text-rose-500 font-bold italic"> ({{ stats.overdue }} are overdue!)</span>
                                </p>
                                <div class="flex flex-col sm:flex-row items-center gap-4">
                                    <Link
                                        :href="route('todos.index')"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-10 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-2xl shadow-xl shadow-indigo-200 dark:shadow-none transition-all transform hover:scale-[1.02] active:scale-95"
                                    >
                                        Manage My Tasks
                                        <svg class="ml-2 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </Link>
                                    <button class="w-full sm:w-auto px-10 py-5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-2xl border border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all">
                                        Quick Settings
                                    </button>
                                </div>
                            </div>
                            <div class="hidden lg:block relative">
                                <div class="h-72 w-72 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/40 dark:to-purple-900/40 rounded-[3rem] flex items-center justify-center transform rotate-6 hover:rotate-0 transition-all duration-700 shadow-2xl">
                                    <svg class="h-40 w-40 text-indigo-500 dark:text-indigo-400 animate-float" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="stat in statCards"
                        :key="stat.name"
                        class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group"
                    >
                        <div class="flex flex-col gap-6">
                            <div
                                :class="[
                                    'h-16 w-16 rounded-2xl flex items-center justify-center transition-all duration-500 group-hover:scale-110',
                                    stat.color === 'indigo' ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : '',
                                    stat.color === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '',
                                    stat.color === 'rose' ? 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' : '',
                                    stat.color === 'amber' ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '',
                                ]"
                            >
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">{{ stat.name }}</p>
                                <p class="text-4xl font-black text-gray-900 dark:text-white transition-colors duration-300">{{ stat.value }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
    100% { transform: translateY(0px); }
}
.animate-float {
    animation: float 4s ease-in-out infinite;
}
</style>
