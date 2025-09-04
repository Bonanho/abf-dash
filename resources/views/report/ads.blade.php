<x-app-layout>

    <x-app.box>
        
        <x-app.table :titles="['Data','Website','Impressões','Clicks','Receita','CPM','CPC' ]">
            @foreach( $reports as $report)
                <tr>
                    <td>{{$report->date}}</td>
                    <td>{{$report->Campaign->name}}</td>
                    <td>{{$report->impressions}}</td>
                    <td>{{$report->clicks}}</td>
                    <td>{{$report->revenue}}</td>
                    <td>{{$report->cpm}}</td>
                    <td>{{$report->cpc}}</td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
