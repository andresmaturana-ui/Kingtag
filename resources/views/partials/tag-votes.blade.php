{{-- King y Toy que recibió un tag en todas sus fotos. La corona se pinta dorada cuando hay algún King. --}}
<span @class(['tag-votes', 'crowned' => $kings > 0])>
    <span class="tag-kings" title="King recibidos">@include('partials.vote-icon', ['vote' => 'king']) {{ $kings }} King</span>
    <span class="tag-toys" title="Toy recibidos">{{ $toys }} Toy</span>
</span>
