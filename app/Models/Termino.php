<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Termino extends Model
{
    protected $table = 'terminos';
    protected $primaryKey = 'id_termino';

    protected $fillable = [
        'id_usuario',
        'acepta_terminos',
        'acepta_politicas'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
