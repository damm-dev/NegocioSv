<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetodoPago extends Model
{
    use HasFactory;

    protected $table = 'metodos_pago'; 
    protected $primaryKey = 'id_metodo_pago'; //llave primaria

    protected $fillable = ['nombre'];

    public function negocios()
    {
        return $this->belongsToMany(Negocio::class, 'negocio_metodo_pago', 'id_metodo_pago', 'id_negocio');
    }
}
