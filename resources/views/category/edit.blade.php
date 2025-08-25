<x-app-layout>
    @php
        $categoryId = (@$category->id);
        $title = title(@$categoryId)." Campanha";
    @endphp
    
    <x-app.box>
        
        <x-app.form.form action="{{route('category-store')}}" method="POST">
            
            <x-app.form.input type="hidden" name="id" :value="@$category->id"></x-app.form.input>

            <div class="row">
                <x-app.form.input size="2" type="text" label="Nome" name="name" :value="@$category->name"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Status" name="status_id" :value="@$category->status_id"></x-app.form.input>
            </div>
            <div class="row">
                <x-app.form.btn size="3" type="back" label="Voltar" :href="route('category')"></x-app.form.btn>
                <x-app.form.btn size="3" type="submit" label="Salvar"></x-app.form.btn>
            </div>
                    
        </x-app.form.form>
        
    </x-app.box>

</x-app-layout>

