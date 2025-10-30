<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['id','Website','Fonte','Fonte Post ID','Fonte Post Título','Tipo', 'Status','Data' ]">
            @foreach( $queuePosts as $queue)
                <tr>
                    <td>{{$queue->id}}</td>
                    <td>{{$queue->Website->name}}</td>
                    <td>{{$queue->Source->name}}</td>
                    <td>{{$queue->source_post_id}}</td>
                    <td>{{$queue->SourcePost ? strLimit($queue->SourcePost->doc->title ?? 'N/A') : 'N/A'}}</td>
                    <td>{{$queue->getType()}}</td>
                    <td>{{$queue->getStatus()}}</td>
                    <td>{{$queue->created_at->format("Y-m-d h:i:s")}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
