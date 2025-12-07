<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioLogro extends Model
{
    protected $table = 'usuario_logros';

    protected $fillable = [
        'id_usuario',
        'id_logro',
        'progreso',
        'completado',
        'fecha_completado'
    ];

    protected $casts = [
        'completado' => 'boolean',
        'fecha_completado' => 'datetime'
    ];

    /**
     * Relación: Pertenece a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Relación: Pertenece a un logro
     */
    public function logro()
    {
        return $this->belongsTo(Logro::class, 'id_logro');
    }
}
