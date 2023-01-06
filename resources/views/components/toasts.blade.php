@props(['message', 'bg'=>'primary', 'text'=>'white'])
<div class="toast-container position-absolute p-3 top-0 end-0">
    <div class="toast align-items-center text-{{ $text }} bg-{{ $bg }} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                {{ $message }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    // hide toast after 3 seconds
    setTimeout(function() {
        document.querySelector('.toast').classList.remove('show');
    }, 3000);
</script>
