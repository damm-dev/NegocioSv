<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoNegocio extends Model
{
    protected $table = 'fotos_negocio';
    protected $primaryKey = 'id_foto';

    protected $fillable = [
        'id_negocio',
        'ruta_foto',
        'orden',
        'descripcion'
    ];

    /**
     * Relación: Pertenece a un negocio
     */
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio');
    }

    /**
     * Accessor para obtener la URL completa de la foto
     */
    public function getFotoUrlAttribute()
    {
        if ($this->ruta_foto) {
            return asset('storage/' . $this->ruta_foto);
        }
        return null;
    }

    /**
     * Agregar foto_url al JSON
     */
    protected $appends = ['foto_url'];
}
