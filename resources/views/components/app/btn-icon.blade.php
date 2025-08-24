@if( $type == "entity")
    <a class="btn btn-primary rounded-5" href="{{$href}}" class="d-flex" >
        {{$text}}
    </a>
@elseif( $type == "plus")
    <a class="btn btn-primary rounded-5" onclick="{{@$onclick}}" class="d-flex" >
        {{$text}}
    </a>
@endif