<nav class="navigation p-1">
    <a href="{{route('home')}}" class="navigation-link"><img src={{asset('info-1.png')}} class="roundedt" width="35" height="35" alt="..." style="float: left"></a>
    <div class="container py-1">
        <a class="navigation-link p-1 px-4 @if(request()->routeIs('home')) active-link
                                    @elseif(request()->routeIs('search*'))active-link @endif" href="{{route('home')}}">
            Suche
        </a>
        <a class="navigation-link p-1 px-4 @if(request()->routeIs('list*'))active-link
                                        @endif" href="{{route('list')}}">
            Städte
        </a>
    </div>
</nav>

    <div class=" mt-4">
        <div class="inputGroup">
    <form method="get" action="{{ route('search') }}" class="input-group container" id="search-city-form">
        <input type="text" class="form-control" name="stadt" autocomplete="off" required="" id="city">
        <label for="city" class="form-label text-center"><span><strong>Stadt:</strong></span></label>

    <button class="btn search-form-button" type="submit"><img src={{asset('zoom.png')}} class="roundedt" width="35" height="35" alt="..." style="float: left"></button>

    </form>
</div>
</div>





