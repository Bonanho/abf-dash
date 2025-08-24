{{-- Example --}}
{{-- <x-app.btn-lite onClick="showModal('modal-1')">Teste Modal</x-app.btn-lite> --}}
{{-- <x-app.modal id="modal-1" title="Teste de Modal"></x-app-modal> --}}

<div id="{{$id}}" class="modal-app">
    <!-- Modal content -->
    <div class="modal-app-content">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex flex-column justify-content-start align-items-start">
                <h4 class="modal-app-title m-0">{{$title}}</h4>
            </div>
            <span class="modal-app-close" onClick="hideModal('{{$id}}');">
                <x-app.icon type="close"></x-app-icon>
            </span>
            
        </div>
        <hr>
        {{$slot}}
    </div>
</div>