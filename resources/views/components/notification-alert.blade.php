@if (session('success'))
    <span class="alert alert-success alert-dismissible alert-fixed alert-autoclose fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </span>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible alert-fixed alert-autoclose fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- if form error --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible alert-fixed alert-autoclose fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
