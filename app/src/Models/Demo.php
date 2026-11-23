<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Demo extends Model
{
    protected $table = 'demo';

    public $timestamps = false;

    protected $fillable = ['note', 'created_at'];
}
