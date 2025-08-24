<x-app-layout>
    @php
        $userId = (@$user->id);
        $title = title($userId)." Usuário";
    @endphp
    
    <x-app.box>
        
        <x-app.form.form action="{{route('user-store')}}" method="POST">
            
            <x-app.form.input type="hidden" name="id" :value="@$user->id"></x-app.form.input>

            <div class="row">
                <x-app.form.input size="2" type="text" label="Nome"  name="name" :value="@$user->name"></x-app.form.input>
                <x-app.form.input size="2" type="text" label="Email" name="email" :value="@$user->email"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Perfil" name="profile_id" :value="@$user->profile_id" :options="@$profiles"></x-app.form.input>

                @if(isset($user))
                    <x-app.form.input size="2" type="select" label="Status" name="status_id" :value="@$user->status_id"></x-app.form.input>
                @endif
            </div>

            <div class="row">
                <x-app.form.btn size="3" type="submit" label="Salvar"></x-app.form.btn>
            </div>
                    
        </x-app.form.form>
        
    </x-app.box>

</x-app-layout>
