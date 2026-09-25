<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['username', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_curator' => 'boolean',
        ];
    }

    /**
     * Admins y curadores pueden borrar fotos que no van con la línea
     * editorial. Solo los admins entran al panel de administración.
     */
    public function canModerate(): bool
    {
        return $this->is_admin || $this->is_curator;
    }

    /**
     * El tag que este usuario reclamó como propio.
     */
    public function tag(): HasOne
    {
        return $this->hasOne(Tag::class, 'artist_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }
}
