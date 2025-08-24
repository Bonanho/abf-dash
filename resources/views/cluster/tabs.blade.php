@if(@$retailId)
    
    @php
        $tabs = [];
        $tabs['Dados Cadastrais'] = ['active'=>'retails/edit', 'href'=>route('retail-edit', codeEncrypt(@$retailId))];
        $tabs['Filiais'] = ['active'=>'branch', 'href'=>route('branch', codeEncrypt(@$retailId))];
        $tabs['Telas'] = ['active'=>'screen-retail', 'href'=>route('screens-retail', codeEncrypt(@$retailId))];
        $tabs['Campanhas'] = ['active'=>'line','href'=>route('line', codeEncrypt(@$retailId))];
        $tabs['Vídeos'] = ['active'=>'video', 'href'=>route('video',['type'=>1,'entityId'=>codeEncrypt(@$retailId)])];

    @endphp

    <x-app.tabs :tabs="$tabs"></x-app.tabs>

@endif