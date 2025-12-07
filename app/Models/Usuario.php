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

    // Relación con logros
    public function logros()
    {
        return $this->belongsToMany(Logro::class, 'usuario_logros', 'id_usuario', 'id_logro')
            ->withPivot('progreso', 'completado', 'fecha_completado')
            ->withTimestamps();
    }

    // Relación con progreso de logros
    public function progresosLogros()
    {
        return $this->hasMany(UsuarioLogro::class, 'id_usuario');
    }

    // Relación con favoritos
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'id_usuario');
    }

    // Relación con negocios favoritos
    public function negociosFavoritos()
    {
        return $this->belongsToMany(Negocio::class, 'favoritos', 'id_usuario', 'id_negocio')
            ->withTimestamps();
    }

    // Relación con seguimientos
    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'id_usuario');
    }

    // Relación con negocios seguidos
    public function negociosSeguidos()
    {
        return $this->belongsToMany(Negocio::class, 'seguimientos', 'id_usuario', 'id_negocio')
            ->withTimestamps();
    }

    // Relación con recomendaciones
    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'id_usuario');
    }

    // Relación con reseñas
    public function resenas()
    {
        return $this->hasMany(Resena::class, 'id_usuario');
    }

    // Relación con negocio (si es usuario tipo negocio)
    public function negocio()
    {
        return $this->hasOne(Negocio::class, 'id_usuario');
    }
}
