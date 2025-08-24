@if( @$href )
    <a href="{{$href}}" class="d-flex" >
@endif

    <button class="btn-lite justify-content-end" onCLick="{{@$onClick}}">{{$slot}}</button>

@if( @$href )
    </a>
@endif