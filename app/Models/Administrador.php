<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Administrador extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'administradores';
    protected $primaryKey = 'id_administrador';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'activo'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
