@props(['label', 'count', 'icon', 'id', 'class' => '', 'url' => '#', 'type' => 'parent'])
<div class="col-md-3">
    <div class="card info-card {{ $class }}" role="button" data-url="{{ $url }}">
        <div class="card-body">
            <h5 class="card-title" {{ $type == 'child' ? 'style=font-size:15px' : '' }}>{{ $label }}</h5>

            <div class="d-flex align-items-center">
                @isset($icon)
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="{{ $icon }}"></i>
                    </div>
                @endisset
                <div class="ps-3">
                    <h4 class="pending-count text-center" id="{{ $id }}">
                        <div class="loading" {{ $type == 'child' ? 'style=--loading-spinner-size:0.6rem' : '' }}>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </h4>
                    @unless($type == 'child')
                        <span class="text-muted small pt-2 ps-1">orders</span>
                    @endunless

                </div>
            </div>
        </div>
    </div>
</div>
