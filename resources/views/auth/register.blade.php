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
                    <img src="../../images/logos/logo1.webp"
                      style="width: 150px;" alt="logo">
                    <h4>S.I.C.A</h4>
                  </div>
  
                  <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <h2 class="text-center text-dark">Cadastre-se</h2>
                    <div class="form-outline m-4">
                      <input type="text" id="name" class="form-control"
                        placeholder="Digite o seu nome" name="name" required />
                    </div>
  
                    <div class="form-outline m-4">
                      <input type="email" id="email" class="form-control"
                        placeholder="Digite o seu email" name="email" required />
                    </div>
  
                    <div class="form-outline m-4">
                      <input type="password" id="password" class="form-control mb-2" placeholder="Digite a sua senha" name="password" required/>
                    </div>

                    <div class="form-outline m-4">
                      <input type="password" id="password_confirmation" class="form-control mb-2" placeholder="Confirme sua senha" name="password_confirmation" required/>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-danger btn-block fa-lg mb-1 w-100">Cadastrar</button>
                    </div>
                  </form>
  
                  <div class="separator">OU</div>
  
                  <div class="text-center">
                      <a href="{{ route('auth.google') }}">
                          <button class="btn btn-outline-danger btn-block fa-lg mb-1 w-100 fw-bold" type="button">CADASTRAR COM GOOGLE</button>
                      </a>
                  </div>
  
                  <div class="d-flex align-items-center justify-content-center mt-2">
                    <p class="mb-0 me-2 text-dark">Já tem uma conta?</p>
                    <a href="{{ route('login') }}">
                      <button type="button" class="btn btn-outline-danger">Login</button>
                    </a>
                  </div>
  
                </div>
              </div>
              <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                <div class="text-white px-3 py-1 p-md-1 mx-md-4">
                  <h4 class="mb-1">Sistema de informação sobre chás avaliados</h4>
                  <p class="small mb-0">A melhor biblioteca online sobre plantas medicinais.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
