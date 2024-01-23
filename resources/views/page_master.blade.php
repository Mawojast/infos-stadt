<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/info-1.png')}}">
    <link href="{{asset('/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/css/app.css')}}" rel="stylesheet">
    <script src="{{asset('/js/jquery-3.7.1.min.js')}}"></script>
    <title>@yield('title')</title>
</head>
<body @yield('background') style="object-fit: contain;">

        @include('header')
        @yield('main')
        @include('footer')

        <script type="module" src="{{asset('/js/bootstrap.bundle.js')}}"></script>
        <script type="module" src="{{asset('/js/app.js')}}"></script>

</body>
</html>
