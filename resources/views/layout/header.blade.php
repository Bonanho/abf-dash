@php
    $uri = $_SERVER['REQUEST_URI'];

    $dashboard = (strpos($uri, 'dashboard')) ? 'active' : '';
    $cluster   = (strpos($uri, 'cluster')) ? 'active' : '';
    $category  = (strpos($uri, 'category')) ? 'active' : '';
    $campaign  = (strpos($uri, 'campaign')) ? 'active' : '';
    $user      = (strpos($uri, 'user')) ? 'active' : '';
@endphp
<header class="header header-sticky p-0 mb-4">
    <div class="container-fluid border-bottom px-4">
        <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px;">
            <svg class="icon icon-lg">
                <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
        </button>
        <ul class="header-nav d-none d-lg-flex">
            <li class="nav-item">
                <span class="nav-link" href="#">
                    <svg class="icon me-2"> <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use> </svg> 
                    Dashboard / CRM
                </span>
            </li>
        </ul>
        <ul class="header-nav ms-auto">
            <li class="nav-item dropdown">
                <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button" aria-expanded="false" data-coreui-toggle="dropdown">
                    <svg class="icon icon-lg theme-icon-active">
                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-contrast"></use>
                    </svg>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
                    <li>
                        <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="light">
                            <svg class="icon icon-lg me-2">
                                <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-sun"></use>
                            </svg> Light
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="dark">
                            <svg class="icon icon-lg me-2">
                                <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-moon"></use>
                            </svg> Dark
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item d-flex align-items-center active" type="button" data-coreui-theme-value="auto">
                            <svg class="icon icon-lg me-2">
                                <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-contrast"></use>
                            </svg> Auto
                        </button>
                    </li>
                </ul>
            </li>
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <svg class="icon icon-lg">
                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                    </svg>
                </a>
            </li>
        </ul>
        <ul class="header-nav">
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link py-0 px-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="true">
                    <div class="avatar avatar-md">
                        <svg class="icon icon-lg theme-icon-active">
                            <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                        </svg>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end pt-0" data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 42px);">
                    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">
                        Conta
                    </div>
                    <a class="dropdown-item" href="#">
                        <svg class="icon me-2">
                            <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
                        </svg> Atualizações <span class="badge badge-sm bg-info ms-2">42</span>
                    </a>
                    <a class="dropdown-item" href="#">
                        <svg class="icon me-2">
                            <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                        </svg> Mensagem <span class="badge badge-sm bg-success ms-2">42</span>
                    </a>
                    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold my-2">
                        <div class="fw-semibold">Configurações</div>
                    </div>
                    <a class="dropdown-item" href="#">
                        <svg class="icon me-2">
                            <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-loop-circular"></use>
                        </svg> Alterar Senha 
                    </a>
                    <a class="dropdown-item" href="#">
                        <svg class="icon me-2">
                            <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-credit-card"></use>
                        </svg> Pagamento
                    </a>
                    <a class="dropdown-item" href="#">
                        <svg class="icon me-2">
                            <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                        </svg> Configurações
                    </a>
                    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">
                        Administradores
                    </div>
                    <a class="dropdown-item" href="{{route('users')}}">
                        <svg class="icon me-2">
                            <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                        </svg> Usuários
                    </a>
                </div>
            </li>
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            <form method="POST" action="{{ route('logout') }}">
            {{-- <form method="POST" action=""> --}}
                @csrf    
                <li class="nav-item dropdown"><a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <a class="nav-link" href="" onclick="event.preventDefault(); this.closest('form').submit();">
                        <svg class="icon me-2"> <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use> </svg> 
                        Sair
                    </a>
                </li>
            </form>
            
        </ul>
    </div>
</header>