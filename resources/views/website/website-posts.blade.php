<x-app-layout>

    <x-app.box>
        @if( $websiteId )
            @include('website._tabs')
            @php
                $titles = ['Id','Fonte','Post ID','Titulo','Content','Status','Data']
            @endphp
        @else
            @php
                $titles = ['Id','Website', 'Fonte','Post ID','Titulo','Content','Status','Data']
            @endphp
        @endif
        
        <x-app.table :titles="$titles">
            @foreach( $posts as $post)
                <tr>
                    {{-- @dd($post->toArray()) --}}
                    <td>{{$post->id}}</td>
                    @if( !$websiteId )
                        <td>{{$post->Website->name}}</td>
                    @endif
                    <td>{{$post->Source->name}}</td>
                    <td>{{$post->website_post_id}}</td>
                    <td>{{strLimit($post->post_title)}} ({{strlen($post->post_title)}})</td>
                    <td>{{strLimit($post->post_content)}} ({{strlen($post->post_content)}})</td>
                    <td>{{$post->getStatus()}}</td>
                    <td>{{$post->created_at->format("Y-m-d h:i:s")}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
