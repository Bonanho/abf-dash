<x-app-layout>

    <x-app.box>
        
        <x-app.page-header name="Websites">
            <x-app.btn-icon type="entity" text="Cadastrar Website" :href="route('website-edit')"></x-app.btn-icon>
        </x-app.page-header>

        <x-app.table :titles="['Id','Nome','URL','Categoria','Empresa','Status','Ações']">
            @foreach( $websites as $website)
                <tr>
                    <td>{{$website->id}}</td>
                    <td>{{$website->name}}</td>
                    <td><a href="{{$website->url}}" target="_blank">{{$website->url}}</a></td>
                    <td>{{$website->Category->name}}</td>
                    <td>{{$website->Company->name}}</td>
                    <td>{{$website->getStatus()}}</td>
                    <td>
                        <x-app.icon type="edit" :href="route('website-edit',codeEncrypt($website->id))"></x-app.icon>
                    </td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
