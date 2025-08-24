<x-app-layout>

    <x-app.box>
        
        <x-app.page-header name="Clusters">
            {{-- <x-app.btn-icon type="entity" text="Cadastrar Cluster" :href="route('cluster-edit')"></x-app.btn-icon>--}}
        </x-app.page-header>

        <x-app.table :titles="['Id','Nome']">
            @foreach( $clusters as $cluster)
                <tr>
                    <td>{{$cluster->id}}</td>
                    <td>{{$cluster->name}}</td>
                    {{-- <td>
                        <x-app.icon type="edit" :href="route('retail-edit',codeEncrypt($retail->id))"></x-app.icon>
                    </td> --}}
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
