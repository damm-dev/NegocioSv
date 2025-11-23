<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;

class Negocio extends Model
{
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    protected $table = 'negocios';

    protected $fillable = [
        'nombreNegocio',
        'email',
        'productos',
        'direccion',
        'metodosPago',
        'categoria',
        'telefono',
        'foto',
    ];

    protected $casts = [
        'metodosPago' => 'array',
    ];
}
