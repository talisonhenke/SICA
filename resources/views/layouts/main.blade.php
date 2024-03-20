<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<head>
	@include('includes.styles')
</head>
<body>
	<div class="main">
		@include('includes.header')
		@include('includes.sessionmsg')
		@yield('content')
		<div id="myFooter" class="myFooter mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 border-top border-white py-4 overflow-hidden">
			@include('includes.footer')
		</div>
	</div>
<body>
	@include('includes.scripts')
</html>