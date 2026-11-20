@extends('layouts.bootstrap')
@section('title', 'Dashboard')
@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h4">Bienvenido, {{ auth()->user()->name }}</h1>
        <p class="mb-0">Tu rol es: <strong>{{ auth()->user()->role }}</strong></p>
    </div>
</div>
@endsection