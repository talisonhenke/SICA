<div class="row align-items-center d-block">
    {{-- <div class="col-12">
      <ul class="nav justify-content-center list-unstyled">
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-whatsapp social-icons-style"></i></a></li>
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-instagram social-icons-style"></i></a></li>
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-linkedin social-icons-style"></i></a></li>
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-github social-icons-style"></i></a></li>
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-facebook social-icons-style"></i></a></li>
        </ul>
    </div> --}}
    <div class="col-12 text-center">
      <span class="text-white">&copy; 2025 S.I.C.A</span>
    </div>
</div>
<script>
    @if(Auth::check())
        // console.log(@json(Auth::user()->id));
        // console.log(auth()->id());
    @else
        console.log("Nenhum usuário logado.");
    @endif
</script>