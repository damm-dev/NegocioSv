<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promocion extends Model
{
    protected $table = 'promociones';
    protected $primaryKey = 'id_promocion';

    protected $fillable = [
        'id_negocio',
        'titulo',
        'descripcion',
        'descuento_porcentaje',
        'codigo_promocional',
        'fecha_inicio',
        'fecha_fin',
        'activa'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    /**
     * Relación: Pertenece a un negocio
     */
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio');
    }

    /**
     * Verificar si la promoción está vigente
     */
    public function getEsVigenteAttribute()
    {
        $hoy = Carbon::now()->startOfDay();
        return $this->activa && 
               $this->fecha_inicio <= $hoy && 
               $this->fecha_fin >= $hoy;
    }

    /**
     * Scope para obtener solo promociones vigentes
     */
    public function scopeVigentes($query)
    {
        $hoy = Carbon::now()->startOfDay();
        return $query->where('activa', true)
                    ->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy);
    }

    /**
     * Agregar es_vigente al JSON
     */
    protected $appends = ['es_vigente'];
}
