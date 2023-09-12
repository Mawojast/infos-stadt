@extends('page_master')
@section('main')
    <div class=" mt-5 text-center">
        <p>Stadt nicht gefunden</p>
    </div>
@endsection

@section('background')
style="background-image: url({{asset('images/mars.jpeg')}}); background-repeat: no-repeat;
background-attachment: fixed;
background-size: cover;"
@endsection

@section('title')
    stadt- Info
@endsection

@section('description')
Allgemeine Informationen über verschiedene Städte weltweit wie die Wettertemperatur, letzten Nachrichten oder die Lage und Einwohnerzahl der Städte.
@endsection

@section('keywords')
Wetter, Temperatur, Windgeschwindigkeit, Nachrichten, Einwohnerzahl, Bekanntheiten
@endsection

