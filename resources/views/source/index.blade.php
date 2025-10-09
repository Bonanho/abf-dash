<x-app-layout>

    <x-app.box>
        
        <x-app.page-header name="Fontes">
            <x-app.btn-icon type="entity" text="Cadastrar Fonte" :href="route('source-edit')"></x-app.btn-icon>
        </x-app.page-header>

        <x-app.table :titles="['Id','Nome','URL','Categoria','Tipo', 'Status','Posts','Ações']">
            @foreach( $sources as $source)
                @php
                    $postsOK = $source->Posts->where("status_id",1)->count();
                @endphp
                <tr>
                    <td width="5%">{{$source->id}}</td>
                    <td width="15%">{{$source->name}}</td>
                    <td width="">{{$source->url}}</td>
                    <td width="10%">{{$source->Category->name}}</td>
                    <td width="10%">{{$source->getType()}}</td>
                    <td width="10%">{{$source->getStatus()}}</td>
                    <td width="5">{{$postsOK}}</td>
                    <td width="5%">
                        <x-app.icon type="edit" :href="route('source-edit',codeEncrypt($source->id))"></x-app.icon>
                    </td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
