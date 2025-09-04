<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['Data','Fonte','Post ID','URL','Status' ]">
            @foreach( $posts as $post)
                <tr>
                    <td>{{$post->created_at->format("Y-m-d h:i:s")}}</td>
                    <td>{{$post->Source->name}}</td>
                    <td>{{$post->post_id}}</td>
                    <td>{{$post->endpoint}}</td>
                    <td>{{$post->status_id}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
