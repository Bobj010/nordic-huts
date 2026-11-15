@extends('layouts.app')

@section('title', 'Вход в систему — Nordic Huts')

@section('content')
    <h1>Вход</h1>
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <input type="email" name="email" required>
        <input type="password" name="password" required>
        <button type="submit">Войти</button>
    </form>
@endsection
