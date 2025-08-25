<x-app-layout>
    @php
        $sourceId = (@$source->id);
        $title = title(@$sourceId)." Campanha";
    @endphp
    
    <x-app.box>
        
        <x-app.form.form action="{{route('source-store')}}" method="POST">
            
            <x-app.form.input type="hidden" name="id" :value="@$source->id"></x-app.form.input>

            <div class="row">
                <x-app.form.input size="2" type="text" label="Nome" name="name" :value="@$source->name"></x-app.form.input>
                <x-app.form.input size="6" type="text" label="URL" name="url" :value="@$source->url"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Categoria" name="category_id" :value="@$source->category_id" :options="@$categories"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Status" name="status_id" :value="@$source->status_id"></x-app.form.input>
            </div>
            <div class="row">
                <x-app.form.btn size="3" type="back" label="Voltar" :href="route('source')"></x-app.form.btn>
                <x-app.form.btn size="3" type="submit" label="Salvar"></x-app.form.btn>
            </div>
                    
        </x-app.form.form>
        
    </x-app.box>

</x-app-layout>

