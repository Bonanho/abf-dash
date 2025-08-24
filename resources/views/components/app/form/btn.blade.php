<div class="{{ classFormDiv( $size ) }}">
<br>
    @if( $type == "submit" )
    
        <button type="submit" class="btn btn-edit btn-action-submit">{{$label}}</button>
    
    @elseif( $type == "back" )
        
        <a class="btn btn-back" type="button" href="{{$href}}">Voltar</a>
    
    @elseif( $type == "edit" )
        
        <a class="btn btn-edit" type="button" href="{{$href}}" class="btn btn-edit btn-action-submit">Editar</a>

    @elseif( $type == "edit-custom" )
        
        <a class="btn btn-edit" type="button" href="{{$href}}" class="btn btn-edit btn-action-submit">{{$label}}</a>

    @elseif( $type == "delete" )

        <button type="submit" class="btn btn-delete btn-action-submit">{{$label}}</button>
        
    @endif

    
</div>