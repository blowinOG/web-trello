<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $table      = 'boards';
    protected $primaryKey = 'board_id';
    public    $timestamps = false;

    protected $fillable = [
        'title', 'owner_user_id', 'is_public',
        'created_datetime', 'updated_datetime',
    ];
}