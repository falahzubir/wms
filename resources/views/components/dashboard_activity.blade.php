@props(['activity', 'time', 'msg'])
<div class="activity-item d-flex">
    <div class="activite-label">{{ $time }}</div>
    <i class='bi bi-circle-fill activity-badge text-secondary align-self-start'></i>
    <div class="activity-content">
        {!! $msg !!}
    </div>
</div><!-- End activity item-->
