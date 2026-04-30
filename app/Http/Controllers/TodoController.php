<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Inertia\Inertia;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Todo::where('user_id', auth()->id());

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $status = $request->status === 'completed';
            $query->where('completed', $status);
        }

        // Filter by Priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sort by order, then by creation date
        $todos = $query->orderBy('order')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Todos/Index', [
            'todos' => $todos,
            'filters' => $request->only(['search', 'status', 'priority', 'category']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'sometimes|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $maxOrder = Todo::where('user_id', auth()->id())->max('order') ?? 0;

        Todo::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'priority' => $validated['priority'] ?? 'Medium',
            'due_date' => $validated['due_date'] ?? null,
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'order' => $maxOrder + 1,
            'completed' => false,
        ]);

        return redirect()->route('todos.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        // Ensure user owns the todo
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'priority' => 'sometimes|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'completed' => 'sometimes|boolean',
        ]);

        $todo->update($validated);

        return redirect()->route('todos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $todo->delete();

        return redirect()->route('todos.index');
    }

    /**
     * Reorder tasks.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:todos,id',
        ]);

        foreach ($request->ids as $index => $id) {
            Todo::where('id', $id)
                ->where('user_id', auth()->id())
                ->update(['order' => $index]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
}
