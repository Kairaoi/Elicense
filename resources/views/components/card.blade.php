@props(['title', 'description', 'route'])

<div class="tab-pane fade @if($loop->first) show active @endif" id="{{ $id }}" role="tabpanel">
    <div class="card mb-4">
        <div class="card-body text-center">
            <h3 class="card-title mb-3">{{ $title }}</h3>
            <p class="card-text">{{ $description }}</p>
            <a href="{{ route($route) }}" class="btn btn-outline-primary btn-lg">View {{ $title }}</a>
        </div>
    </div>
</div>
