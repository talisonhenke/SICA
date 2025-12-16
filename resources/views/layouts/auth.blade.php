<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<head>
    @include('includes.styles')
    <style>
        @media (max-width: 992px) {
            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="main">
        @include('includes.auth_header')
        @yield('content')
    </div>

    <body>
        @include('includes.scripts')

</html>
