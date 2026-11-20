@extends('layouts.bootstrap')
@section('title', 'Verificación')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header">Verificación en dos pasos</div>
            <div class="card-body">
                <p class="text-muted">Te enviamos un código de 6 dígitos a tu correo. Vence en 5 minutos.</p>
                <form method="POST" action="{{ route('two-factor.verify') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="code" class="form-label">Código</label>
                        <input id="code" name="code" maxlength="6" inputmode="numeric"
                               class="form-control @error('code') is-invalid @enderror" required autofocus>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn btn-primary w-100">Verificar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection