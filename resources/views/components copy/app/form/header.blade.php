@php
    $style = (isset($textAlign) && $textAlign=="right") ? "text-align:right;" : "";
@endphp
<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" style="{{$style}}">
    {{ $slot }}
</h2>
<br>
