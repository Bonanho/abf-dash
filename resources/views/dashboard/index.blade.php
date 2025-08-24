@php
    $uri = $_SERVER['REQUEST_URI'];
    $asset = asset('assets');
@endphp
<x-app-layout>

    <div class="row mb-4">
        <div class="col-xl-6 mb-4 mb-xl-0">
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="fw-bolder h5 m-0">Taxa de cliques e impressões</h4>
                </div>
                <div class="card-body py-2">
                    <p class="m-0 small">Por cliques, CTR e impressões</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="row vertical-center">
                                <div class="col-3">
                                    <svg class="icon icon-xxl">
                                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-cursor"></use>
                                    </svg>                           
                                </div>
                                <div class="col-9">
                                    <div class="fs-4 fw-semibold">1.272</div>
                                    <p class="m-0">Clicks</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="row vertical-center">
                                <div class="col-3">
                                    <svg class="icon icon-xxl">
                                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-thumb-up"></use>
                                    </svg>                           
                                </div>
                                <div class="col-9">
                                    <div class="fs-4 fw-semibold">0,22%</div>
                                    <p class="m-0">Click Rate <span class="fs-6 fw-normal">(CTR)</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="row vertical-center">
                                <div class="col-3">
                                    <svg class="icon icon-xxl">
                                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-screen-smartphone"></use>
                                    </svg>                           
                                </div>
                                <div class="col-9">
                                    <div class="fs-4 fw-semibold">464.838</div>
                                    <p class="m-0">Impressions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="fw-bolder h5 m-0">Custo por clique</h4>
                </div>
                <div class="card-body py-2">
                    <p class="m-0 small">Por Custo, CPC e CPM</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="row vertical-center">
                                <div class="col-3">
                                    <svg class="icon icon-xxl">
                                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-plus"></use>
                                    </svg>                           
                                </div>
                                <div class="col-9">
                                    <div class="fs-4 fw-semibold">R$15,06</div>
                                    <p class="m-0">CPM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <div class="row vertical-center">
                                <div class="col-3">
                                    <svg class="icon icon-xxl">
                                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-happy"></use>
                                    </svg>                           
                                </div>
                                <div class="col-9">
                                    <div class="fs-4 fw-semibold">R$5,97</div>
                                    <p class="m-0">CPC</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-white bg-secondary">
                        <div class="card-body">
                            <div class="row vertical-center">
                                <div class="col-3">
                                    <svg class="icon icon-xxl">
                                        <use xlink:href="{{$asset}}/vendors/@coreui/icons/svg/free.svg#cil-money"></use>
                                    </svg>
                                </div>
                                <div class="col-9">
                                    <div class="fs-4 fw-semibold">R$7.592,11</div>
                                    <p class="m-0">Cost</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">Informações</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">

                            <table class="table border table-hover  rounded">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">
                                            <div class="border-start border-start-4 border-start-info px-3">
                                                <div class="small text-body-secondary text-truncate">Cidade</div>
                                            </div>
                                        </th>
                                        <th scope="col">
                                            <div class="border-start border-start-4 border-start-danger px-3">
                                                <div class="small text-body-secondary text-truncate">Cliques</div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>Alta Floresta</td>
                                        <td>417</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">2</th>
                                        <td>Carlinda</td>
                                        <td>211</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">3</th>
                                        <td>Sinop</td>
                                        <td>151</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">4</th>
                                        <td>Sorriso</td>
                                        <td>140</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">5</th>
                                        <td>Apiacás</td>
                                        <td>87</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">6</th>
                                        <td>Lucas do Rio Verde</td>
                                        <td>27</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">7</th>
                                        <td>Nova Mutum</td>
                                        <td>25</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">8</th>
                                        <td>Nova Bandeirantes</td>
                                        <td>23</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-6">
                                    <div class="border-start border-start-4 border-start-warning px-3 mb-3">
                                        <div class="small text-body-secondary text-truncate">Total Reach Base Rock</div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="border-start border-start-4 border-start-success px-3 mb-3">
                                        <div class="border-start border-start-4 border-start-info px-3 mb-3">
                                            <div class="small text-body-secondary text-truncate">Família</div>
                                        </div>  
                                    </div>
                                </div>

                            </div>

                            <hr class="mt-0" />

                            <div class="progress-group">
                                <div class="progress-group-header">
                                    <div>Cesta</div>
                                    <div class="ms-auto fw-semibold me-2">14.366</div>
                                    <div class="text-body-secondary small">95.77%</div>
                                </div>
                                <div class="progress-group-bars">
                                    <div class="progress progress-thin">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 95.77%" aria-valuenow="96.77" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-group mb-5">
                                <div class="progress-group-header">
                                    <div>Caldos</div>
                                    <div class="ms-auto fw-semibold me-2">7.215</div>
                                    <div class="text-body-secondary small">48.1%</div>
                                </div>
                                <div class="progress-group-bars">
                                    <div class="progress progress-thin">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 48.1%" aria-valuenow="48.1" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

   
                            <div class="progress-group">
                                <div class="progress-group-header">
                                    <div>Caldos</div>
                                    <div class="ms-auto fw-semibold me-2">3.751</div>
                                    <div class="text-body-secondary small"><small>(sem filhos)</small></div>

                                </div>
                                <div class="progress-group-bars">
                                    <div class="progress progress-thin">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 90%" aria-valuenow="3.751" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-group">
                                <div class="progress-group-header">
                                    <div>Cesta</div>
                                    <div class="ms-auto fw-semibold me-2">6.752</div>
                                    <div class="text-body-secondary small"><small>(sem filhos)</small></div>
                                </div>
                                <div class="progress-group-bars">
                                    <div class="progress progress-thin">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 80%" aria-valuenow="6.752" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="progress-group">
                                <div class="progress-group-header">
                                    <div>Caldos</div>
                                    <div class="ms-auto fw-semibold me-2">34.63</div>
                                    <div class="text-body-secondary small"><small>(com filhos)</small></div>
                                </div>
                                <div class="progress-group-bars">
                                    <div class="progress progress-thin">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 83%" aria-valuenow="34.63" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="progress-group">
                                <div class="progress-group-header">
                                    <div>Cesta</div>
                                    <div class="ms-auto fw-semibold me-2">7.613</div>
                                    <div class="text-body-secondary small"><small>(com filhos)</small></div>
                                </div>
                                <div class="progress-group-bars">
                                    <div class="progress progress-thin">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 93%" aria-valuenow="7.613" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <table class="table table-striped table-bordered table-hover border rounded">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Campanha</th>
                                <th scope="col">Impressions</th>
                                <th scope="col">Clicks</th>
                                <th scope="col">CPM</th>
                                <th scope="col">CPC</th>
                                <th scope="col">Revenue (Adv Currency)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Base Caldos</th>
                                <td>50.180</td>
                                <td>34</td>
                                <td>10,9</td>
                                <td>16,09</td>
                                <td>547,07</td>
                            </tr>
                            <tr>
                                <th scope="row">Base Cesta</th>
                                <td>106.563</td>
                                <td>85</td>
                                <td>10,6</td>
                                <td>13,29</td>
                                <td>1.130,04</td>
                            </tr>   
                            <tr>
                                <th scope="row">Canais</th>
                                <td>145.703</td>
                                <td>375</td>
                                <td>22,62</td>
                                <td>8,79</td>
                                <td>3.296</td>
                            </tr>   
                            <tr>
                                <th scope="row">LookAlike</th>
                                <td>162.392</td>
                                <td>778</td>
                                <td>16,13</td>
                                <td>3,37</td>
                                <td>2.619</td>
                            </tr>                        
                        </tbody>
                    </table>

                </div>
                
            </div>
            
        </div>

    </div>
    

</x-app-layout>
