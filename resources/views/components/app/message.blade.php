@if(Session::has('success') || Session::has('error'))

    @php
        if(Session::has('success')){
            $typeMessage = "success";
            $classMessage = "success";
        }elseif(Session::has('error')){
            $typeMessage = "error";
            $classMessage = "danger alert-important";
        }
    @endphp

    <div class="alert alert-{{$classMessage}} alert-dismissible fade show" role="alert" data-bs-delay="5000">
        {{ Session::get($typeMessage) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.querySelectorAll('div.alert:not(.alert-important)').forEach(function(alert) {
                alert.style.transition = 'opacity 0.35s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 350);
            });
        }, 7000);
    });
</script>