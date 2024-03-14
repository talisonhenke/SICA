@extends('layouts.main')
@section('content')
<div class="container">
    <input type="text" id="searchInput" class="form-control mb-3 mt-4" placeholder="Pesquisar...">
    <div class="mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 border-top border-white py-4 overflow-hidden">
        <div class="list-group" id="plantList">
            @foreach ($plants as $plant)
                <a href="#" class="list-group-item list-group-item-action text-center">{{ $plant->popular_name }} ({{ $plant->scientific_name }})</a>
            @endforeach
        </div>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            $('#plantList a').each(function() {
                var plantName = $(this).text().toLowerCase();
                if (plantName.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
