<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['Fonte','Post ID','URL','Status','Data']">
            @foreach( $posts as $post)
                <tr>
                    <td>{{$post->Source->name}}</td>
                    <td>{{$post->post_origin_id}}</td>
                    <td>{{$post->endpoint}}</td>
                    <td>{{$post->status_id}}</td>
                    <td>{{$post->created_at->format("Y-m-d h:i:s")}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
