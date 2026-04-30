@extends('layout')

@section('content')
    <h1>Create Todo</h1>
    <form action="{{ route('todos.store') }}" method="POST">
        @csrf
        <input type="text" name="title" placeholder="Title" required>
        <button type="submit">Add</button>
    </form>
    <a href="{{ route('todos.index') }}">Back to List</a>
@endsection