<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'user_id';
    public    $timestamps = false;

    protected $fillable = [
        'email', 'password_hash', 'name',
        'created_datetime', 'updated_datetime',
    ];
}