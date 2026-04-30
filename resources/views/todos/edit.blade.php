@extends('layout')

@section('content')
    <h1>Edit Todo</h1>
    <form action="{{ route('todos.update', $todo) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="title" value="{{ $todo->title }}" required>
        <label>
            <input type="checkbox" name="completed" value="1" {{ $todo->completed ? 'checked' : '' }}> Completed
        </label>
        <button type="submit">Update</button>
    </form>
    <a href="{{ route('todos.index') }}">Back to List</a>
@endsection