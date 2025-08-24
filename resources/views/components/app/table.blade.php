<table class="table table-striped table-hover border rounded">
    
    <thead>
        <tr>
            @foreach( $titles as $title)
                <th>{{$title}}</th>
            @endForeach
        </tr>
    </thead>
    
    <tbody>
        {{ $slot }}
    </tbody>
    
    @if( @$totals )
        <thead>
            <tr>
                @foreach( $totals as $total)
                    <th>{{$total}}</th>
                @endForeach
            </tr>
        </thead>
    @endif

</table>