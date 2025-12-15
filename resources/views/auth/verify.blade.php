@extends('layouts.main')

@section('content')
<div class="container">
    <h1>Verifique seu e-mail</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <p>
        Antes de continuar, verifique seu e-mail clicando no link que enviamos.
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">
            Reenviar e-mail de verificação
        </button>
    </form>
</div>
@endsection
