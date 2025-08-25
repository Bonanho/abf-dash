<x-app-layout>

    <x-app.box>
        
        <x-app.page-header name="Categorias">
            <x-app.btn-icon type="entity" text="Cadastrar Categorias" :href="route('category-edit')"></x-app.btn-icon>
        </x-app.page-header>

        <x-app.table :titles="['Id','Nome','Status']">
            @foreach( $categories as $category)
                <tr>
                    <td>{{$category->id}}</td>
                    <td>{{$category->name}}</td>
                    <td>{{$category->getStatus()}}</td>
                    <td>
                        <x-app.icon type="edit" :href="route('category-edit',codeEncrypt($category->id))"></x-app.icon>
                    </td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
