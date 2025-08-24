<div class="{{ classFormDiv( $size ) }}">
    
    <x-app.box>
    
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <h3 class="title">{{$value}}</h3>
            @if(isset($icon))
            <i class="{{$icon}}" style="float:right;font-size: 50px;"></i>
            @endif
        </div>

        @php
            $valueAlign = (isset($valueAlign) && $valueAlign=="left") ? "left" : "right";
        @endphp

        @if(isset($title))
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" style="text-align:{{$valueAlign}};">
                {{ $title }}
            </h2>
        @endif

        {{ $slot }}

    </x-app.box>

</div>