<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $table = 'seguimientos';
    protected $primaryKey = 'id_seguimiento';

    protected $fillable = [
        'id_usuario',
        'id_negocio'
    ];

    /**
     * Relación: Pertenece a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /**
     * Relación: Pertenece a un negocio
     */
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio');
    }
}
