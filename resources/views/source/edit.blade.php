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
                <x-app.form.input size="4" type="text" label="URL" name="url" :value="@$source->url"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Tipo" name="type_id" :value="@$source->type_id" :options="@$types"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Categoria" name="category_id" :value="@$source->category_id" :options="@$categories"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Status" name="status_id" :value="@$source->status_id"></x-app.form.input>
            </div>
            <hr>** LIST ENDPOINT ** <br><br>
            <div class="row">
                <x-app.form.input size="6" type="text" label="LIST ENDPOINT" name="tpt_list_endpoint" :value="@$source->template->listEndpoint"></x-app.form.input>
            </div>
            <hr>** WP ENDPOINT ** <br><br>
            <div class="row">
                <x-app.form.input size="6" type="text" label="WP ENDPOINT" name="tpt_wp_endpoint" :value="@$source->template->wpEndpoint"></x-app.form.input>
            </div>
            <hr>** TEMPLATE CONFIG ** <br><br>
            <div class="row">
                <x-app.form.input size="12" type="text" label="Home New" name="tpt_home_new" :value="@$source->template->homeNew"></x-app.form.input>
                <x-app.form.input size="12" type="text" label="Title" name="tpt_title" :value="@$source->template->title"></x-app.form.input>
                <x-app.form.input size="12" type="text" label="Content" name="tpt_content" :value="@$source->template->content"></x-app.form.input>
            </div>
            <div class="row">
                <x-app.form.btn size="3" type="back" label="Voltar" :href="route('source')"></x-app.form.btn>
                <x-app.form.btn size="3" type="submit" label="Salvar"></x-app.form.btn>
            </div>
                    
        </x-app.form.form>
        
    </x-app.box>

</x-app-layout>

