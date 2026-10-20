<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table      = 'cards';
    protected $primaryKey = 'card_id';
    public    $timestamps = false;

    protected $fillable = [
        'board_id', 'column_id', 'sort_index', 'title',
        'content', 'color', 'is_archived',
        'created_datetime', 'updated_datetime',
    ];
}