@extends('page_master')
@section('main')
@endsection

@section('background')

style="background-image: url({{asset($backgroundImagePath)}}); background-repeat: no-repeat;
background-attachment: fixed;
background-size: cover;"
@endsection

@section('title')
Stadt - Info
@endsection

@section('description')
Allgemeine Informationen über verschiedene Städte weltweit wie die Wettertemperatur, letzten Nachrichten oder die Lage und Einwohnerzahl der Städte.
@endsection

@section('keywords')
Wetter, Nachrichten, Einwohnerzahl, Bekanntheiten, Städte
@endsection
