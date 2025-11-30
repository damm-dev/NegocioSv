<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'password',
        'estado',
        'fecha_creacion'
    ];

    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'id_usuario', 'id_usuario');
    }

    public function intereses()
    {
        return $this->hasMany(Interes::class, 'id_usuario', 'id_usuario');
    }
}
