@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <input type="password" name="password" class="form-control mb-3"
           placeholder="Nova senha" required>

    <input type="password" name="password_confirmation" class="form-control mb-3"
           placeholder="Confirmar senha" required>

    <button class="btn btn-success w-100">Redefinir senha</button>
</form>
@endsection
