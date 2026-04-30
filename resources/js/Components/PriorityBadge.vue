<script setup>
const props = defineProps({
    priority: {
        type: String,
        required: true,
    },
});

const colors = {
    High: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800',
    Medium: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
    Low: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
};
</script>

<template>
    <span
        :class="[
            'px-2.5 py-0.5 rounded-full text-xs font-bold border',
            colors[priority] || colors.Medium
        ]"
    >
        {{ priority }}
    </span>
</template>
