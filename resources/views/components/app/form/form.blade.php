<form action="{{ $action }}" method="{{ $method }}" enctype="{{@$enctype}}" >
    @csrf
    @method( $method )

    {{ $slot }}
</form>