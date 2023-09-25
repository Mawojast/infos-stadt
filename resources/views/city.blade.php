@extends('page_master')
@section('main')

<div class="container pt-3" >
    <div class="main-content p-3 mt-3">

        <div class="d-flex flex-row justify-content-between">
            <div>
                <h2 class="d-inline-flex text-justify">{{$weather->city}}</h2>
                <img src="{{asset($weatherIconPath)}}" class="d-inline-flex" style="height: 50px;" alt="weather-icon-{{$weather->description}}">
            </div>
            <div>
                <p class="font-weight-bold text-right p-1 time-info-box"><strong>{{$dateTime}}</strong></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="d-inline-block">
                    <p class="weather-info-box p-1">{{round($weather->temperature)}}&#8451;</p>
                </div>
                <div class="d-inline-block">
                    <p class="weather-info-box p-1">{{$weather->description}}</p>
                </div>
                <div class="d-inline-block">
                    <p class="weather-info-box p-1">{{$weather->windSpeed}} km/h Windgeschwindigkeit</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3 city-description">
                    <p>{{$cityDescription}}<br><br><small><i>@if(strlen($cityDescription) > 0) Dieser Text ist KI generiert und kann Fehler enthalten.@endif</i></small></p>
                </div>
            </div>
        </div>
    </div>
    <div style="height: 1.5rem"></div>
    <div class="container">
        @foreach($articles as $article)
        <a href="{{$article->url}}">
        <div class="row news-article p-3 mb-3">
            <div class="col-md  ">
                <img src="@if (filter_var($article->imagePath, FILTER_VALIDATE_URL)) {{$article->imagePath}} @else {{asset($article->imagePath)}} @endif" class="card-img-top article-image" alt="...">
            </div>
            <div class="col-md">
                <h3 class="card-title pt-2">{{$article->title}}</h3>
                <p class="card-text pt-2 article-description">{{$article->description}}</p>
                <p class="card-text article-info"><small>{{date('d.m.Y',strtotime($article->publishedAt))}} @if (!empty($article->sourceName)) - {{$article->sourceName}} @endif</small></p>
            </div>
        </div>
        </a>
        @endforeach
    </div>
</div>
@endsection

@section('background')
style="background-image: url({{asset($weatherBackgroundImagePath)}}); background-repeat: no-repeat;
background-attachment: fixed;
background-size: cover;"
@endsection

@section('title')
    {{$weather->city}} - {{round($weather->temperature)}}&#8451; {!!$titleIcons[0] ?? '' !!} {!!$titleIcons[1] ?? ''!!}
@endsection

@section('description')
Für die Stadt {{$weather->city}} die Einwohnerzahl, aktuelle Wetterdaten oder auch Nachrichten gezeigt.
@endsection

@section('keywords')
Wetter, Temperatur, Windgeschwindigkeit, Nachrichten, Einwohnerzahl, Bekanntheiten, {{$weather->city}}
@endsection
