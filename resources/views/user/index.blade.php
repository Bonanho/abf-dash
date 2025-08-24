<x-app-layout>

    <x-app.box>

        <x-app.page-header name="">
            <x-app.btn-icon type="entity" text="Cadastrar Usuário" :href="route('user-edit')"></x-app.btn-icon>
        </x-app.page-header>

        <x-app.form.form action="{{route('users-filter')}}" method="POST">
            
            <x-app.form.input size="2" type="select" label="Status" name="status_id" :value="@$filter['status_id']" onchange="submit()"></x-app.form.input>

        </x-app.form.form>
        
        <x-app.table :titles="['Id','Nome','Email','Perfil','Editar']">
            @foreach( $users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->profileName()}}</td>
                    <td>
                        <x-app.icon type="edit" :href="route('user-edit',codeEncrypt($user->id))"></x-app.icon>
                    </td>
                </tr>
            @endForeach
        </x-app.table>

    </x-app.box>

</x-app-layout>
