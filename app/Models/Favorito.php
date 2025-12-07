<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    protected $table = 'favoritos';
    protected $primaryKey = 'id_favorito';

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
