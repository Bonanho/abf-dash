<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['Id','Website', 'Fonte','Post ID','Titulo','Status','Data']">
            @foreach( $posts as $post)
                <tr>
                    {{-- @dd($post->toArray()) --}}
                    <td>{{$post->id}}</td>
                    <td>{{$post->Website->name}}</td>
                    <td>{{$post->Source->name}}</td>
                    <td>{{$post->website_post_id}}</td>
                    <td>{{strLimit($post->post_title)}}</td>
                    <td>{{$post->getStatus()}}</td>
                    <td>{{$post->created_at->format("Y-m-d h:i:s")}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
