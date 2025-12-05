<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resena extends Model
{
    use HasFactory;

    protected $table = 'resenas';
    protected $primaryKey = 'id_resena';

    protected $fillable = [
        'id_negocio',
        'id_usuario',
        'comentario',
        'calificacion',
    ];

    // Una reseña pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Una reseña pertenece a un negocio
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}
