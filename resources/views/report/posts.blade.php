<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['Data','Website','Fonte','Posts' ]">
            @foreach( $reports as $report)
                <tr>
                    <td>{{$report->date}}</td>
                    <td>{{$report->Website->name}}</td>
                    <td>{{$report->Source->name}}</td>
                    <td>{{$report->posts}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
