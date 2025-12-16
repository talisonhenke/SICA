@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <input type="email" name="email" class="form-control mb-3"
           placeholder="Seu e-mail" required>

    <button class="btn btn-primary w-100">Enviar link de recuperação</button>
</form>
@endsection
