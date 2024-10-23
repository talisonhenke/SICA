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
  
                  <form>
                    <div data-mdb-input-init class="form-outline m-4">
                      <input type="email" id="form2Example11" class="form-control"
                        placeholder="Digite o seu email" />
                    </div>
  
                    <div data-mdb-input-init class="form-outline m-4">
                      <input type="password" id="form2Example22" class="form-control mb-2" placeholder="Digite a sua senha"/>
                      <a class="text-muted" href="#!">Esqueceu sua senha?</a>
                    </div>
  
                    <div class="text-center">
                      <button data-mdb-button-init data-mdb-ripple-init class="btn btn-danger btn-block fa-lg mb-1 w-100" id type="button">Entrar</button>
                    </div>
                    <div class="separator">OU</div>
                    <div class="text-center">
                        <button data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-danger btn-block fa-lg mb-1 w-100 fw-bold" id type="button">ENTRAR COM GOOGLE</button>
                      </div>
                    <div class="d-flex align-items-center justify-content-center mt-2">
                      <p class="mb-0 me-2 text-dark">Não tem conta?</p>
                      <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-danger">Cadastre-se</button>
                    </div>
  
                  </form>
  
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