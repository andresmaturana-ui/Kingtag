<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Collection;

/**
 * El ranking cuenta cuántos grafitis distintos tiene cada tag en la ciudad.
 * Si dos tags empatan, va primero el que se registró antes.
 */
class Ranking
{
    /**
     * @return Collection<int, Tag>
     */
    public function all(): Collection
    {
        return Tag::query()
            ->whereHas('graffitis')
            ->withCount('graffitis')
            ->orderByDesc('graffitis_count')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, Tag>
     */
    public function top(int $limit = 20): Collection
    {
        return $this->all()->take($limit)->values();
    }

    /**
     * La posición de un tag y los tags que tiene justo arriba y justo abajo.
     *
     * @return array{position: int|null, above: Tag|null, below: Tag|null}
     */
    public function around(Tag $tag): array
    {
        $all = $this->all();
        $index = $all->search(fn (Tag $t) => $t->id === $tag->id);

        if ($index === false) {
            return ['position' => null, 'above' => null, 'below' => null];
        }

        return [
            'position' => $index + 1,
            'above' => $all->get($index - 1),
            'below' => $all->get($index + 1),
        ];
    }
}
