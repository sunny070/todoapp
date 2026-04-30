<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PriorityBadge from '@/Components/PriorityBadge.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import draggable from 'vuedraggable';
import pickBy from 'lodash/pickBy';
import throttle from 'lodash/throttle';

const props = defineProps({
    todos: Array,
    filters: Object,
});

const form = useForm({
    title: '',
    priority: 'Medium',
    due_date: '',
    category: '',
    description: '',
});

const filterForm = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
    priority: props.filters.priority || '',
    category: props.filters.category || '',
});

const editingTodo = ref(null);
const editForm = useForm({
    title: '',
    priority: 'Medium',
    due_date: '',
    category: '',
    description: '',
    completed: false,
});

const addTodo = () => {
    if (!form.title.trim()) return;
    form.post(route('todos.store'), {
        onSuccess: () => {
            form.reset();
            // Optional: Close modal if using one for adding
        },
    });
};

const deleteTodo = (id) => {
    if (confirm('Are you sure you want to delete this todo?')) {
        router.delete(route('todos.destroy', id));
    }
};

const editTodo = (todo) => {
    editingTodo.value = todo;
    editForm.title = todo.title;
    editForm.priority = todo.priority;
    editForm.due_date = todo.due_date ? todo.due_date.split('T')[0] : '';
    editForm.category = todo.category || '';
    editForm.description = todo.description || '';
    editForm.completed = todo.completed;
};

const updateTodo = () => {
    editForm.put(route('todos.update', editingTodo.value.id), {
        onSuccess: () => {
            editingTodo.value = null;
        },
    });
};

const toggleComplete = (todo) => {
    router.put(route('todos.update', todo.id), {
        completed: !todo.completed,
    }, { preserveScroll: true });
};

// Reordering logic
const onDragEnd = () => {
    const ids = props.todos.map(todo => todo.id);
    axios.post(route('todos.reorder'), { ids })
        .catch(error => {
            console.error('Failed to update order', error);
            // Optionally refresh the page to restore the original order
        });
};

// Filtering logic
watch(filterForm, throttle(() => {
    router.get(route('todos.index'), pickBy(filterForm.value), {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

const resetFilters = () => {
    filterForm.value = {
        search: '',
        status: '',
        priority: '',
        category: '',
    };
};
</script>

<template>
    <Head title="Todos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent dark:from-indigo-400 dark:to-purple-400">
                    My Advanced Tasks
                </h2>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total: {{ todos.length }}</span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-[calc(100vh-64px)] transition-colors duration-300">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Search and Filters -->
                <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-1">
                            <input
                                v-model="filterForm.search"
                                type="text"
                                placeholder="Search tasks..."
                                class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 py-2 px-4 focus:border-indigo-500 focus:ring-indigo-500 transition-all text-sm"
                            />
                        </div>
                        <select
                            v-model="filterForm.status"
                            class="rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 py-2 px-4 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                        </select>
                        <select
                            v-model="filterForm.priority"
                            class="rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 py-2 px-4 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        >
                            <option value="">All Priorities</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                        <div class="flex gap-2">
                            <input
                                v-model="filterForm.category"
                                type="text"
                                placeholder="Category..."
                                class="flex-1 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 py-2 px-4 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            />
                            <button
                                @click="resetFilters"
                                class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                title="Reset Filters"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Todo Card -->
                <div class="mb-8 overflow-hidden bg-white dark:bg-gray-800 shadow-xl sm:rounded-3xl border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <div class="p-8">
                        <form @submit.prevent="addTodo" class="space-y-4">
                            <div class="flex gap-4">
                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="What needs to be done?"
                                    class="flex-1 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 py-4 px-6 focus:border-indigo-500 focus:ring-indigo-500 transition-all text-lg"
                                    required
                                />
                                <button
                                    type="submit"
                                    class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg transition-all transform hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                                    :disabled="form.processing"
                                >
                                    Add Task
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Priority</label>
                                    <select v-model="form.priority" class="flex-1 rounded-xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 py-2 text-sm focus:ring-indigo-500">
                                        <option value="High">High</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Low">Low</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Due Date</label>
                                    <input type="date" v-model="form.due_date" class="flex-1 rounded-xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 py-2 text-sm focus:ring-indigo-500" />
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Category</label>
                                    <input type="text" v-model="form.category" placeholder="e.g. Work" class="flex-1 rounded-xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 py-2 text-sm focus:ring-indigo-500" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Todo List with Draggable -->
                <draggable
                    v-if="todos.length > 0"
                    v-model="props.todos"
                    item-key="id"
                    class="space-y-4"
                    handle=".drag-handle"
                    @end="onDragEnd"
                >
                    <template #item="{ element: todo }">
                        <div
                            class="group bg-white dark:bg-gray-800 p-5 rounded-3xl shadow-md border border-transparent hover:border-indigo-100 dark:hover:border-indigo-900 transition-all duration-300 flex items-center gap-4"
                            :class="{ 'opacity-60 bg-gray-50/50 dark:bg-gray-900/50 grayscale-[0.5]': todo.completed }"
                        >
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-300 dark:text-gray-600 hover:text-indigo-500 transition-colors p-1">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    <path d="M5 8a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2zm10-8a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2z" />
                                </svg>
                            </div>

                            <button
                                @click="toggleComplete(todo)"
                                class="h-7 w-7 rounded-full border-2 flex items-center justify-center transition-all duration-300 transform hover:scale-110"
                                :class="todo.completed ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-gray-300 dark:border-gray-700 text-transparent hover:border-indigo-400'"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <PriorityBadge :priority="todo.priority" />
                                    <span v-if="todo.category" class="text-[10px] font-black uppercase tracking-widest text-indigo-500 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded">
                                        {{ todo.category }}
                                    </span>
                                    <span v-if="todo.due_date" class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ new Date(todo.due_date).toLocaleDateString() }}
                                    </span>
                                </div>
                                <h3
                                    class="text-lg font-semibold truncate transition-all duration-300"
                                    :class="todo.completed ? 'text-gray-400 line-through dark:text-gray-600' : 'text-gray-800 dark:text-gray-200'"
                                >
                                    {{ todo.title }}
                                </h3>
                                <p v-if="todo.description" class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1">
                                    {{ todo.description }}
                                </p>
                            </div>

                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button @click="editTodo(todo)" class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteTodo(todo.id)" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </draggable>

                <!-- Empty State -->
                <div v-else class="text-center py-20 bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center justify-center h-24 w-24 rounded-3xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-500 mb-8">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 dark:text-gray-200 mb-3">No matching tasks</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Try adjusting your filters or add a new task to get things moving!</p>
                </div>
            </div>
        </div>

        <!-- Advanced Edit Modal -->
        <div v-if="editingTodo" class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/80 backdrop-blur-md transition-opacity" @click="editingTodo = null"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700">
                    <div class="p-10">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-3xl font-black text-gray-900 dark:text-gray-100">Edit Task</h3>
                            <button @click="editingTodo = null" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <form @submit.prevent="updateTodo" class="space-y-6">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Task Title</label>
                                <input
                                    v-model="editForm.title"
                                    type="text"
                                    class="w-full rounded-2xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 py-4 px-5 focus:ring-indigo-500 text-lg font-semibold"
                                    required
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Priority</label>
                                    <select v-model="editForm.priority" class="w-full rounded-2xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 py-3 px-4 focus:ring-indigo-500">
                                        <option value="High">High</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Low">Low</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Due Date</label>
                                    <input type="date" v-model="editForm.due_date" class="w-full rounded-2xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 py-3 px-4 focus:ring-indigo-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Category</label>
                                    <input v-model="editForm.category" type="text" class="w-full rounded-2xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 py-3 px-4 focus:ring-indigo-500" />
                                </div>
                                <div class="flex items-center gap-3 pt-6">
                                    <button
                                        type="button"
                                        @click="editForm.completed = !editForm.completed"
                                        class="h-8 w-8 rounded-lg border-2 flex items-center justify-center transition-all duration-300"
                                        :class="editForm.completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-200 dark:border-gray-700 text-transparent'"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Mark as completed</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                                <textarea
                                    v-model="editForm.description"
                                    rows="4"
                                    class="w-full rounded-2xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 py-4 px-5 focus:ring-indigo-500 resize-none"
                                    placeholder="Add more details about this task..."
                                ></textarea>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <button
                                    type="submit"
                                    class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black py-4 px-8 rounded-2xl shadow-xl transition-all transform hover:scale-[1.02] active:scale-95"
                                    :disabled="editForm.processing"
                                >
                                    Save Changes
                                </button>
                                <button
                                    type="button"
                                    @click="editingTodo = null"
                                    class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-black py-4 px-8 rounded-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-600"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.drag-handle:active {
    cursor: grabbing;
}
.sortable-ghost {
    opacity: 0.3;
    background: #eef2ff !important;
    border: 2px dashed #6366f1 !important;
}
</style>
