<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - Seite nicht gefunden</title>
    <style>

        .main_content {
            display: grid;
            place-items:  center;
            height: 50rem;
        }
        img{
            width: 10rem;
            height: 10rem;
        }
    </style>
</head>
<body>
    <div class="main_content">
        <div>
        <img src="{{asset('images/error/404.jpg')}}">
        <p>Diese Seite ist leider nicht verfügbar. <br>
            Möchten Sie nach einer Stadt suchen?<p>


                <form method="get" action="{{ route('search') }}" class="input-group container" id="search-city-form">
                    <input type="text" class="form-control" name="stadt" autocomplete="off" required="" id="city" placeholder="Stadt">
                <button class="btn search-form-button" type="submit">Suche</button>


            </div>

    </div>
    </div>

</body>
</html>
