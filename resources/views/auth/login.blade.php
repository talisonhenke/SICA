@extends('layouts.auth')
@section('content')
<section class="gradient-form" style="background-color: #eee;">
    <div class="container">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="col-xl-10">
          <div class="card rounded-3 text-black">
            <div class="row g-0">
              <div class="col-lg-6">
                <div class="card-body mx-md-4">
  
                    <div class="text-center">
                      <img src="../../images/logos/logo1.png"
                        style="width: 150px;" alt="logo">
                      <h4>S.I.C.A</h4>
                    </div>
    
                    <form method="POST" action="{{ route('login.post') }}">
                      @csrf
                      <div data-mdb-input-init class="form-outline m-4">
                          <input type="email" id="form2Example11" class="form-control" name="email" placeholder="Digite o seu email" required />
                      </div>
                  
                      <div data-mdb-input-init class="form-outline m-4">
                          <input type="password" id="form2Example22" class="form-control mb-2" name="password" placeholder="Digite a sua senha" required />
                          <a class="text-muted" href="#!">Esqueceu sua senha?</a>
                      </div>
                  
                      <div class="text-center">
                          <button type="submit" class="btn btn-danger btn-block fa-lg mb-1 w-100">Entrar</button>
                      </div>
                      <div class="separator">OU</div>

                      <div class="text-center">
                        <a href="{{ route('auth.google') }}">
                            <button class="btn btn-outline-danger btn-block fa-lg mb-1 w-100 fw-bold" type="button">ENTRAR COM GOOGLE</button>
                        </a>
                        <div class="d-flex align-items-center justify-content-center mt-2">
                          <p class="mb-0 me-2 text-dark">Não tem conta?</p>
                          <a href="{{ route('register') }}">
                            <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-danger">Cadastre-se</button>
                          </a>
                      </div>
                      </div>
                    </form>
                </div>
              </div>
              <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                <div class="text-white px-3 py-1 p-md-1 mx-md-4">
                  {{-- Nome do sistema definido --}}
                  <h4 class="mb-1">Sistema de informação sobre chás avaliados</h4>
                  <p class="small mb-0">Biblioteca online de artigos sobre plantas medicinais.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
    </div>
  </section>
@endsection