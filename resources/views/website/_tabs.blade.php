@php
    $tabWebsiteId = codeEncrypt( (@$website->id) ?? $websiteId );
    $tabs = [
        'Website' => ["active"=>"websites/edit", "href"=>route('website-edit',$tabWebsiteId) ],
        'Fontes'  => ["active"=>"website/web-source", "href"=>route('website-source',$tabWebsiteId)],
        'Posts'  => ["active"=>"website/web-posts", "href"=>route('website-posts',$tabWebsiteId)],
    ];
@endphp

<x-app.tabs :tabs="$tabs"></x-app.tabs>
