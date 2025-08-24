@if($src)
<video width="320" height="240" controls preload="none">
        <source src="https://{{$src}}" type="video/mp4">
    <source src="movie.ogg" type="video/ogg">
</video>
@endif