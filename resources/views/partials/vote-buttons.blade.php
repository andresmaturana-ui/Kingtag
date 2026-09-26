{{-- Botón King (corona) de una foto. Tocarlo de nuevo lo quita. --}}
@php($compact = $compact ?? false)
<div @class(['votes', 'compact' => $compact]) data-votes>
    @foreach (['king' => ['King', $photo->likers_count, $photo->kinged ?? false]] as $vote => [$label, $count, $on])
        @auth
            <form method="POST" action="{{ route('photos.vote', [$photo, $vote]) }}" data-vote>
                @csrf
                <button @class(['vote', $vote, 'on' => $on]) aria-pressed="{{ $on ? 'true' : 'false' }}" aria-label="{{ $label }}">
                    @include('partials.vote-icon')<span class="vote-label">{{ $label }}</span> <span class="vote-count" data-count>{{ $count }}</span>
                </button>
            </form>
        @else
            <a @class(['vote', $vote]) href="{{ route('login') }}" aria-label="{{ $label }}">
                @include('partials.vote-icon')<span class="vote-label">{{ $label }}</span> <span class="vote-count">{{ $count }}</span>
            </a>
        @endauth
    @endforeach
</div>
