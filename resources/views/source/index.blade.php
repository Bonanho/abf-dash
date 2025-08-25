<x-app-layout>

    <x-app.box>
        
        <x-app.page-header name="Fontes">
            <x-app.btn-icon type="entity" text="Cadastrar Fonte" :href="route('source-edit')"></x-app.btn-icon>
        </x-app.page-header>

        <x-app.table :titles="['Id','Nome','URL','Categoria','Status']">
            @foreach( $sources as $source)
                <tr>
                    <td>{{$source->id}}</td>
                    <td>{{$source->name}}</td>
                    <td>{{$source->url}}</td>
                    <td>{{$source->Category->name}}</td>
                    <td>{{$source->getStatus()}}</td>
                    <td>
                        <x-app.icon type="edit" :href="route('source-edit',codeEncrypt($source->id))"></x-app.icon>
                    </td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
