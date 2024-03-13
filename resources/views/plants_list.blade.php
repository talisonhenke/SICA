@extends('layouts.main')
@section('content')
<div class="myFooter mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 border-top border-white py-4 overflow-hidden">
	<div class="list-group">
		@foreach ($plants as $plant)
			<a href="#" class="list-group-item list-group-item-action">{{ $plant->popular_name }}</a>
		@endforeach
	</div>
</div>
@endsection