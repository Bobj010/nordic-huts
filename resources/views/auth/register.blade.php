@extends('layouts.app')

@section('title', 'Регистрация — Nordic Huts')

@section('content')
    <h1>Регистрация</h1>
    <form method="POST" action="{{ route('register.post') }}">
        @csrf
        <input type="text" name="name" required>
        <input type="email" name="email" required>
        <input type="password" name="password" required>
        <input type="password" name="password_confirmation" required>
        <button type="submit">Зарегистрироваться</button>
    </form>
@endsection
