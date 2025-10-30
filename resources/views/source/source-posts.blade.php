<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['Fonte','Post ID','URL','Status','Data']">
            @foreach( $posts as $post)
                <tr>
                    <td>{{$post->Source->name}}</td>
                    <td>{{$post->post_origin_id}}</td>
                    <td>{{$post->endpoint}}</td>
                    @if($post->status_id==-1)
                        <td title="{{json_encode($post->error)}}">{{$post->getStatus()}}</td>
                    @else
                        <td>{{$post->getStatus()}}</td>
                    @endif
                    <td>{{$post->created_at->format("Y-m-d h:i:s")}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
