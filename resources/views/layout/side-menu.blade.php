@php
    $uri = $_SERVER['REQUEST_URI'];

    $dashboard      = (strpos($uri, 'dashboard')) ? 'active' : '';
    $category       = (strpos($uri, 'category')) ? 'active' : '';
    $websiteQueue   = (strpos($uri, 'website-queue')) ? 'active' : '';
    $websitePosts   = (strpos($uri, 'website-posts')) ? 'active' : '';
    $website        = (!$websiteQueue && !$websitePosts && strpos($uri, 'website')) ? 'active' : '';
    $sourcePost     = (strpos($uri, 'source-post')) ? 'active' : '';
    $source         = (!$sourcePost && strpos($uri, 'source') && !strpos($uri, 'web-source') ) ? 'active' : '';
@endphp

<nav class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom py-2">
        <div class="sidebar-brand">
            <div class="sidebar-brand-full">
                <div class="fs-3 d-flex align-items-center">
                    <span class="me-1">WEBSITES - IA</span> 
                </div>
            </div>
            <div class="sidebar-brand-narrow" width="32" height="32" alt="Logo">
                WEB-IA
            </div>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">

        <li class="nav-item">
            <a class="nav-link {{ $dashboard }}" href="{{route('dashboard')}}">
                <svg class="nav-icon">
                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
                </svg>  
                Dashboard
            </a>
        </li>

        <li class="nav-divider"></li>

        <li class="nav-title mt-1">Websites</li>
        <li class="nav-item">
            <a class="nav-link {{ $website }}" href="{{route('website')}}">
                <svg class="nav-icon">
                    <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-basket"></use>
                </svg>
                Websites
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $websitePosts }}" href="{{route('posts')}}">
                <svg class="nav-icon">
                    <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-basket"></use>
                </svg>
                Websites Posts
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $websiteQueue }}" href="{{route('queue')}}">
                <svg class="nav-icon">
                    <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-basket"></use>
                </svg>
                Fila Posts
            </a>
        </li>

        <li class="nav-title mt-1">Admin</li>
        <li class="nav-item">
            <a class="nav-link {{ $category }}" href="{{route('category')}}">
                <svg class="nav-icon">
                    <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-tag"></use>
                </svg>
                Categorias
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $source }}" href="{{route('source')}}">
                <svg class="nav-icon">
                    <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-basket"></use>
                </svg>
                Fontes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $sourcePost }}" href="{{route('source-post')}}">
                <svg class="nav-icon">
                    <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-basket"></use>
                </svg>
                Fontes Posts
            </a>
        </li>
        
        {{-- <li class="nav-divider"></li>
        <li class="nav-title mt-1">Relatórios</li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('report_post')}}">
                <svg class="nav-icon">
                    <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-chart-pie"></use>
                </svg>
                Posts
            </a>
        </li> --}}
        {{-- <li class="nav-item">
            <a class="nav-link" href="{{route('report_ads')}}">
                <svg class="nav-icon">
                    <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-chart-pie"></use>
                </svg>
                ADS
            </a>
        </li> --}}
                
    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
      <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</nav>