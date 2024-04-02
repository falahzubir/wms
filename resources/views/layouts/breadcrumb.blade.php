@props(['title' => 'Untitled', 'crumbList' => false])
<div class="pagetitle">
    <h1>{{ $title }}</h1>
    <!-- Breadcrumb disabled for time being -->
    @if($crumbList)
    <nav>
        <ol class="breadcrumb">
            @if(isset($crumbList))
                @foreach($crumbList as $crumb)
                <li class="breadcrumb-item {{ $crumb['active'] ?? '' }}"><a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a></li>
                @endforeach
            @endif
        </ol>
    </nav>
    @endif
</div><!-- End Page Title -->
