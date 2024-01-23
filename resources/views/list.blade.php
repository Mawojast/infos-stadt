@extends('page_master')
@section('main')

<div class="container pt-3">

    <div class=" text-center">
        <ul class="listContainer">
        @for($i = 65; $i <= 90; $i++)
            @php
                $char = strtolower(html_entity_decode("&#".$i.";"));
            @endphp
            {{-- <li class="d-inline-block letter p-1"><a href="{{route('listByLetter', $char)}}"><strong>&#{{$i}};</strong></a></li> --}}
            <li class="p-2 d-inline"><a href="#{{$char}}"><strong class="listLetterLink">&#{{$i}};</strong></a></li>
        @endfor
        </ul>
    </div>
    <div class="row">
        <ul>
            <h2 class="letterLabel ps-1">&#65</h2>
            @php
            $letterCode = 65;
            $letterCodeChar = strtolower(html_entity_decode("&#".$letterCode.";"));
            $lastSettedLabel = '';
            $labelSetted = false;
            @endphp
            @for($i = 0; $i < count($cities); $i++)
                @php
                    if(ord(substr($cities[$i]->name, 0, 1)) === $letterCode + 1 ){
                        $letterCode++;
                        $lastSettedLabel = $letterCode;
                        $id = strtolower(html_entity_decode('&#'.$letterCode.';'));
                        if($labelSetted){
                            $labelSetted = false;
                        }else{
                            echo '<h2 class="letterLabel ps-1 mt-1" id="'.$id.'">&#'.$letterCode.'</h2>';
                        }
                    }
                    if(ord(substr($cities[$i]->name, 0, 1)) > $letterCode + 1 && ord(substr($cities[$i-1]->name, 0, 1)) == $letterCode){
                        
                        if($lastSettedLabel != ord(substr($cities[$i]->name, 0, 1))){

                            for($j = $i - 1; $j >= 0; $j--){
                                if(ord(substr($cities[$j]->name, 0, 1)) == ord(substr($cities[$i]->name, 0, 1))){

                                    $lastSettedLabel = $letterCode;
                                    $labelId = $letterCode+1;

                                    $id = strtolower(html_entity_decode('&#'.$labelId.';'));
                                    $labelSetted = true;
                                    $letter = $letterCode + 1;

                                    echo '<h2 class="letterLabel ps-1 mt-1" id="'.$id.'">&#'.$letter.'</h2>';
                                }
                            }
                        }
                    }
                @endphp
                <li style="text-indent: 1rem;" class="py-1 city-link"><a href="{{route('city', $cities[$i]->name)}}">{{$cities[$i]->name}}</a></li>
            @endfor
        </ul>
    </div>
    <button type="button" id="scroll-to-top" class="btn">&#8593</button>
</div>
@endsection
@section('background')
style="background-color: #bfbfbf;"
@endsection
@section('title')
    Städteliste
@endsection

@section('description')
Eine Liste der Städte über die es Informationen gibt.
@endsection


