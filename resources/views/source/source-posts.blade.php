<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['Id','Fonte','URL','Tipo Source','Status','Data']">
            @foreach( $posts as $post)
                <tr>
                    <td>{{$post->id}}</td>
                    <td>{{$post->Source->name}}</td>
                    <td title="{{$post->endpoint}}"><a href="{{$post->endpoint}}" target="_blank">{{strLimit($post->endpoint)}}</td>
                    <td>{{$post->Source->getType()}}</td>
                    @if($post->status_id==-1)
                        <td title="{{json_encode($post->error)}}">{{$post->getStatus()}}</td>
                    @else
                        <td>{{$post->getStatus()}}</td>
                    @endif
                    <td data-order="{{$post->created_at->format("Ymdhis")}}">{{$post->created_at->format("Y-m-d h:i:s")}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
