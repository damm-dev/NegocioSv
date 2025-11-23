<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Negocio;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
    ];

    public function negocios()
    {
        return $this->hasMany(Negocio::class);
    }
}
