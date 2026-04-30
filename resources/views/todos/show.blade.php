@extends('layout')

@section('content')
    <h1>Todo Details</h1>
    <p>Title: {{ $todo->title }}</p>
    <p>Status: {{ $todo->completed ? 'Completed' : 'Incomplete' }}</p>
    <a href="{{ route('todos.edit', $todo) }}">Edit</a>
    <a href="{{ route('todos.index') }}">Back to List</a>
@endsection