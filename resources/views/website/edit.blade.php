<x-app-layout>
    @php
        $websiteId = (@$website->id);
        $title = title(@$websiteId)." Campanha";
        $keywords = (@$website->doc->useKeywords) ? implode("|||",$website->doc->keywords) : "";
    @endphp
    
    <x-app.box>
        @include('website._tabs')

        <x-app.form.form action="{{route('website-store')}}" method="POST">
            
            <x-app.form.input type="hidden" name="id" :value="@$website->id"></x-app.form.input>

            <div class="row">
                <x-app.form.input size="2" type="select" label="Empresa" name="company_id" :value="@$website->company_id" :options="@$companies"></x-app.form.input>
            </div>
            <div class="row">
                <x-app.form.input size="2" type="text" label="Nome" name="name" :value="@$website->name"></x-app.form.input>
                <x-app.form.input size="6" type="text" label="URL" name="url" :value="@$website->url"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Categoria" name="category_id" :value="@$website->category_id" :options="@$categories"></x-app.form.input>
                <x-app.form.input size="2" type="select" label="Status" name="status_id" :value="@$website->status_id"></x-app.form.input>
            </div>
            <hr>
            <div class="row">
                <x-app.form.input size="3" type="text" label="WP-User" name="wpuser" :value="@$website->config->wpUser"></x-app.form.input>
                <x-app.form.input size="3" type="text" label="WP-Pass" name="wppass" :value="@$website->config->wpPass"></x-app.form.input>
                <x-app.form.input size="4" type="text" label="Site Map" name="sitemap" :value="@$website->config->siteMap"></x-app.form.input>
            </div>
            <hr>
            <div class="row">
                <x-app.form.input size="12" type="text" label="Palavras-Chave" name="keywords" :value="$keywords"></x-app.form.input>
            </div>
            <hr>
            <div class="row">
                <x-app.form.btn size="3" type="back" label="Voltar" :href="route('website')"></x-app.form.btn>
                <x-app.form.btn size="3" type="submit" label="Salvar"></x-app.form.btn>
            </div>
                    
        </x-app.form.form>
        
    </x-app.box>

</x-app-layout>

