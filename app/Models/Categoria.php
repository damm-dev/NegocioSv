<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';

    protected $fillable = [
        'nombre'
    ];

    public function intereses()
    {
        return $this->hasMany(Interes::class, 'id_categoria');
    }
}
