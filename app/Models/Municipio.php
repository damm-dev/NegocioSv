<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';
    protected $primaryKey = 'id_municipio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'id_departamento'
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }

    public function perfiles()
    {
        return $this->hasMany(Perfil::class, 'id_municipio', 'id_municipio');
    }
}
