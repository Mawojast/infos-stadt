<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/info-1.png')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="{{asset('/css/app.css')}}" rel="stylesheet">
    <title>@yield('title')</title>
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>
<body @yield('background') style="object-fit: contain;">
        @include('header')

        @yield('main')
        @include('footer')
        <div class="d-flex justify-content-center m-5 spinner-box">
            <div class="spinner-grow" style="width: 3rem; height: 3rem; display: none; background-color: #0b0d0f;" role="status">
                <span class="sr-only"></span>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script type="module" src="{{asset('/js/app.js')}}"></script>



        <script type="module">



</script>
</body>
</html>
