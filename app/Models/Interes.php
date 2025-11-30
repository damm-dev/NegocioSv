<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interes extends Model
{
    protected $table = 'intereses';
    protected $primaryKey = 'id_interes';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_categoria'
    ];
}
