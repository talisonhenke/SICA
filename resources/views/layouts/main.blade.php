<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<head>
	@include('includes.styles')
	<title>S.I.C.A - Sistema de Informação Sobre Chás Avaliados</title>
	<link rel="icon" type="image/png" href="{{ asset('images/logo_sica.png') }}">

</head>
<body>
	<div class="main d-flex flex-column min-vh-100">
		@include('includes.header')
		@include('includes.sessionmsg')

		<main class="flex-fill">
			@yield('content')
		</main>

		<div id="myFooter" class="myFooter mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 border-top border-white py-4 overflow-hidden">
			@include('includes.footer')
		</div>
	</div>
	@include('includes.scripts')
</body>
</html>
