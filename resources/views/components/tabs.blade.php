@props(['tabs'])

<ul class="nav nav-pills mb-4 justify-content-center flex-wrap">
    @foreach ($tabs as $tab)
        <li class="nav-item">
            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $tab['id'] }}-tab" data-bs-toggle="tab" href="#{{ $tab['id'] }}" role="tab">
                <i class="{{ $tab['icon'] }}"></i><br>
                {{ $tab['title'] }}
            </a>
        </li>
    @endforeach
</ul>
