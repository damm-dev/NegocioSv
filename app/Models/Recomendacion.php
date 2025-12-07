<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{
    protected $table = 'recomendaciones';
    protected $primaryKey = 'id_recomendacion';

    protected $fillable = [
        'id_usuario',
        'id_negocio',
        'medio'
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
