<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios'; 

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'email',
        'password',
        'id_estado_usuario'
    ];

    protected $hidden = [
        'password'
    ];

    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'id_usuario');
    }

    public function intereses()
    {
        return $this->hasMany(Interes::class, 'id_usuario');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoUsuario::class, 'id_estado_usuario');
    }
}
