<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class BoardColumn extends Model
{
    protected $table = 'columns';
    protected $fillable = ['board_id', 'title', 'position'];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'column_id')->orderBy('position');
    }
}
