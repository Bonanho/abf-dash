<!DOCTYPE html>
<html lang="pt-br">
    @php
        $uri = $_SERVER['REQUEST_URI'];
        $asset = asset('assets');
    @endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>{{ config('app.name', 'CRM') }}</title>

    {{-- Meta Tags --}}
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keyword" content="">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{$asset}}/core/css/coreui.min.css">
    <link rel="stylesheet" href="{{$asset}}/vendors/@coreui/chartjs/css/coreui-chartjs.css">
    <link rel="stylesheet" href="{{$asset}}/css/style.css">

    {{-- JS --}}
    <script src="{{$asset}}/js/config.js"></script>
    <script src="{{$asset}}/js/color-modes.js"></script>

    {{-- Icons --}}
    <link rel="shortcut icon" href="{{$asset}}/img/icon.png">
    <link rel="apple-touch-icon precomposed" href="{{$asset}}/img/icon.png">
    <link rel="icon" href="{{$asset}}/img/icon.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <script src="{{$asset}}/js/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
</head>
<body>

    @include('layout.side-menu')

    <div class="wrapper d-flex flex-column min-vh-100">
        
        @include('layout.header')

        <div class="body flex-grow-1">
            <div class="container-fluid px-lg-5">

                <x-app.message></x-app.message>

                {{ $slot }}

                <script src="{{$asset}}/js/datatable.js"></script>

            </div>

        </div>

        <footer class="footer px-4">
            <div>CRM</a> © 2025 - </div>
            <div class="ms-auto">Por <a href="#">CRM</a></div>
        </footer>

    </div>

    {{-- @include('components.load') --}}

    {{-- JS --}}
    <script src="{{$asset}}/vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="{{$asset}}/vendors/@coreui/chartjs/js/coreui-chartjs.js"></script>

</body>
</html>
