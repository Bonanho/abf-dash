@php
    $tabWebsiteId = codeEncrypt( (@$website->id) ?? $websiteId );
    $tabs = [
        'Website' => ["active"=>"websites/edit", "href"=>route('website-edit',$tabWebsiteId) ],
        'Fontes'  => ["active"=>"website/w-source", "href"=>route('website-source',$tabWebsiteId)],
    ];
@endphp

<x-app.tabs :tabs="$tabs"></x-app.tabs>
