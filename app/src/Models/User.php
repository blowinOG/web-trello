<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password_hash'];
    protected $hidden = ['password_hash'];

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class, 'owner_id');
    }

    public function assignedCards(): HasMany
    {
        return $this->hasMany(Card::class, 'assignee_id');
    }
}
