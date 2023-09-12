@extends('page_master')
@section('main')

<div class="container pt-3">
    <div class="row text-center">
        <ul>
            <li class="d-inline-block letter p-1"><a href="{{route('list')}}"><strong>Alle</strong></a></li>

            @for($i = 65; $i <= 90; $i++)
            @php
                $char = strtolower(html_entity_decode("&#".$i.";"));
            @endphp
            <li class="d-inline-block letter p-1 @if( $char == $letter) activeLetterList  @endif"><a href="{{route('listByLetter', $char)}}"><strong>&#{{$i}};</strong></a></li>
            @endfor
        </ul>
    </div>
    <div class="row">
        <ul>
            <h2 class="letterLabel ps-1">{{Str::upper($letter)}}</h2>
            @foreach($cities as $city)
            <li style="text-indent: 1rem;" class="py-1 city-link"><a href="{{route('city', $city->name)}}">{{$city->name}}</a></li>
            @endforeach
        </ul>
    </div>
    <a id="scroll-to-top" href="#"><i class="fa fa-chevron-up"></i></a>
</div>
@endsection
@section('background')
style="background-color: #bfbfbf;"
@endsection
@section('title')
{{Str::upper($letter)}} - Städte
@endsection

@section('description')
Eine Liste der Städte, die mit dem Buchstaben '{{Str::upper($letter)}}' beginnen und über die es Informationen gibt.
@endsection
