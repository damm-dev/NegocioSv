<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logro extends Model
{
    protected $table = 'logros';
    protected $primaryKey = 'id_logro';

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'meta',
        'tipo'
    ];

    /**
     * Relación: Un logro puede ser desbloqueado por muchos usuarios
     */
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_logros', 'id_logro', 'id_usuario')
            ->withPivot('progreso', 'completado', 'fecha_completado')
            ->withTimestamps();
    }

    /**
     * Relación: Progreso de usuarios en este logro
     */
    public function progresos()
    {
        return $this->hasMany(UsuarioLogro::class, 'id_logro');
    }
}
