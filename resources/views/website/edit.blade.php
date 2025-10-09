<x-app-layout>
    @php
        $websiteId = (@$website->id);
        $title = title(@$websiteId)." Campanha";
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

            <div class="form-group col-md-12">
                <div class="grumft-card-gray">
                    <label>Palavras-Chave</label>
                    <div class="input-group mb-3">
                        <input type="text" id="new_keyword" name="new_keyword" class="{{classForm()}}" value="" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-custom btn-sm" type="button" onclick="keyWordAdd()">
                                <x-app.icon type="plus"></x-app.icon>
                            </button>
                        </div>
                    </div>
                    @if( is_array($website->doc->keywords) )
                        @foreach($website->doc->keywords as $keyword)
                            <span class="badge badge-secondary">
                                {{$keyword}} &nbsp; &nbsp;
                                @php
                                    $keyWordFunc = "keyWordRemove('".$keyword."')";
                                @endphp
                                <x-app.icon type="minus" :onclick="$keyWordFunc"></x-app.icon>
                            </span>
                            &nbsp;
                        @endforeach
                    @endif
                </div>
            </div>

            <hr>
            <div class="row">
                <x-app.form.btn size="3" type="back" label="Voltar" :href="route('website')"></x-app.form.btn>
                <x-app.form.btn size="3" type="submit" label="Salvar"></x-app.form.btn>
            </div>
            
        </x-app.form.form>
        
    </x-app.box>

    <script>
        function keyWordAdd() {
            keyWordAdd = $('#new_keyword').val();
            if(keyWordAdd==""){
                alert('Domínio inválido');
            }
            else if(keyWordAdd.includes('www') || keyWordAdd.includes('http')){
                alert('Favor remover www, http, ou https.');
            } else {
                websiteId = "{{ $websiteId }}";
                $.when($.post( "{{route('website-keyword')}}", { "action":"add", "websiteId": websiteId, "keyword": keyWordAdd } ) )
                .then( (response) => {
                    response = JSON.parse(response);
                    if( response.error ){
                        alert(response.error);
                    } else {
                        location.reload();
                    }
                });
            }
        }

        function keyWordRemove(keyword) {
            websiteId = "{{ $websiteId }}";
            $.when($.post( "{{route('website-keyword')}}", { "action":"remove", "websiteId": websiteId, "keyword": keyword} ) )
            .then(() => location.reload())   
        }
    </script>

</x-app-layout>

