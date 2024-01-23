<nav class="navigation p-1">
    <a href="{{route('home')}}" class="navigation-link"><img src={{asset('info-1.png')}} class="roundedt" width="35" height="35" alt="..." style="float: left"></a>
    <div class="container py-1 navigation-link-container">
        <a class="navigation-link p-1 px-4 @if(request()->routeIs('home')) active-link
                                    @elseif(request()->routeIs('search*'))active-link @endif" href="{{route('home')}}">
            Suche
        </a>
        <a class="navigation-link p-1 px-4 @if(request()->routeIs('list*'))active-link
                                        @endif" href="{{route('list')}}">
            Liste
        </a>
    </div>
</nav>

    <div class=" mt-4">
        <div class="inputGroup">
    <form method="get" action="{{ route('search') }}" class="input-group container" id="search-city-form">
        @csrf
        <input type="text" class="form-control" name="stadt" autocomplete="off" required="" id="city">

        <label for="city" class="form-label text-center"><span><strong>Stadt:</strong></span></label>

    <button class="btn search-form-button" type="submit">
        <img src={{asset('zoom.png')}} class="search-button-image" width="35" height="35" alt="..." style="float: left">
        <div class="d-flex justify-content-center spinner-box">
            <div class="spinner-grow" style="width: 2.2rem; height: 2.2rem; display: none; background-color: #e8e8ec;" role="status">
                <span class="sr-only"></span>
            </div>
        </div>
    </button>

    </form>
</div>
</div>
