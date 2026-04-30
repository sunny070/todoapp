<template>
  <div class="glass-container">
    <h1 class="text-2xl font-bold mb-4">Edit Todo</h1>
    <form @submit.prevent="saveEdit">
      <input v-model="form.title" class="glass-input mb-2" placeholder="Title" required />
      <label class="block mb-2">
        <input type="checkbox" v-model="form.completed" /> Completed
      </label>
      <button type="submit" class="glass-btn">Save</button>
      <button @click.prevent="cancelEdit" class="glass-btn glass-btn-danger ml-2">Cancel</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';

const page = usePage();
const todo = page.props.todo;
const form = useForm({
  title: todo.title,
  completed: todo.completed,
});

const saveEdit = () => {
  router.put(route('todos.update', todo.id), form, {
    onSuccess: () => router.visit(route('todos.index')),
  });
};

const cancelEdit = () => {
  router.visit(route('todos.index'));
};
</script>

<style scoped>
.glass-container {
  background: rgba(255, 255, 255, 0.15);
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 2rem;
  margin-top: 2rem;
}
.glass-input {
  background: rgba(255,255,255,0.3);
  border: none;
  border-radius: 8px;
  padding: 0.5rem 1rem;
  outline: none;
  box-shadow: 0 2px 8px rgba(31,38,135,0.07);
}
.glass-btn {
  background: rgba(255,255,255,0.25);
  border: none;
  border-radius: 8px;
  padding: 0.5rem 1.2rem;
  margin: 0 0.2rem;
  cursor: pointer;
  transition: background 0.2s;
  box-shadow: 0 2px 8px rgba(31,38,135,0.07);
}
.glass-btn:hover {
  background: rgba(255,255,255,0.4);
}
.glass-btn-danger {
  background: rgba(255,0,0,0.15);
  color: #b91c1c;
}
.glass-btn-danger:hover {
  background: rgba(255,0,0,0.25);
}
</style>
