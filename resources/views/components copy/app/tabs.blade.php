@php
    $uri         = $_SERVER['REQUEST_URI'];
    $activeClass = "tab-active";
@endphp

<div>
    <ul class="nav nav-pills">
        
        @foreach( $tabs as $_key => $_value)
            @php
                $_value = (object) $_value;
                $_class  = (strpos($uri, $_value->active)) ? $activeClass : 'tab-not-active';
            @endphp

            <li class="nav-item {{$_class}}">
                <a class="nav-link nav-tab" href="{{$_value->href}}" id="dataTab">
                    <i class="fa fa-cogs"></i> {{$_key}}
                </a>
            </li>
        @endForeach

    </ul>
    <hr>
</div>

