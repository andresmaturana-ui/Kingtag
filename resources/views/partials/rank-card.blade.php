<a class="rank-card" href="{{ route('tags.show', $t) }}">
    <span class="pos">#{{ $pos }}</span>
    @if ($img = $t->imageUrl())
        <img src="{{ $img }}" alt="" loading="lazy">
    @endif
    <span>{{ $t->text }}</span>
</a>
