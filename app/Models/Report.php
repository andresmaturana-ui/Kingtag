<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un aviso de un usuario sobre una foto o un comentario que no debería estar.
 * Los admins los ven en el panel, en «Reportes».
 */
#[Fillable(['user_id', 'photo_id', 'comment_id', 'reason'])]
class Report extends Model
{
    public const REASONS = [
        'ofensivo' => 'Ofensivo o violento',
        'acoso' => 'Acoso o amenaza',
        'personal' => 'Muestra datos o la cara de alguien',
        'spam' => 'Spam o publicidad',
        'otro' => 'Otro motivo',
    ];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function reasonLabel(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }
}
