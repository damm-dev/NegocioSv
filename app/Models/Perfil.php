<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfiles';
    protected $primaryKey = 'id_perfil';

    protected $fillable = [
        'id_usuario',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'foto',
        'id_municipio',
        'descripcion',
        'ubicacion_activa'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    // Agregar foto_url al JSON automáticamente
    protected $appends = ['foto_url'];

    // Accessor para obtener la URL completa de la foto
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return null;
    }
}
