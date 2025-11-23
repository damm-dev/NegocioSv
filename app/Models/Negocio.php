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
        'password',
        'productos',
        'direccion',
        'metodosPago',
        'categoria',
        'telefono',
        'foto',
        'descripcion'
    ];

    protected $casts = [
        'metodosPago' => 'array',
    ];

    protected $hidden = [
        'password',
    ];
}
