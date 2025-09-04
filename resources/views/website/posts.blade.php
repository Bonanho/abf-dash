<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['Data','Website', 'Fonte','Post ID','Titulo','Status' ]">
            @foreach( $posts as $post)
                <tr>
                    <td>{{$post->created_at->format("Y-m-d h:i:s")}}</td>
                    <td>{{$post->Website->name}}</td>
                    <td>{{$post->Source->name}}</td>
                    <td>{{$post->post_id}}</td>
                    <td>{{$post->post_title}}</td>
                    <td>{{$post->getStatus()}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
