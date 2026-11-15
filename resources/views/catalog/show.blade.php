@extends('layouts.app')

@section('title', $house->name . ' — Nordic Huts')

@section('content')
    <h1>{{ $house->name }}</h1>
@endsection
