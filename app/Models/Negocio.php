<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Negocio extends Model
{
    use HasFactory;

    protected $table = 'negocios';
    protected $primaryKey = 'id_negocio';  //clave primaria

    protected $fillable = [
        'id_usuario',
        'id_municipio',
        'nombre',
        'descripcion',
        'direccion',
        'latitud',
        'longitud',
        'telefono',
        'email_contacto',
        'logo',
        'estado_verificacion'
    ];

    // Relación: Un negocio pertenece a un Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Relación: Un negocio tiene muchas categorías
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'negocio_categoria', 'id_negocio', 'id_categoria');
    }

    // Relación: Un negocio acepta muchos métodos de pago
    public function metodosPago()
    {
        return $this->belongsToMany(MetodoPago::class, 'negocio_metodo_pago', 'id_negocio', 'id_metodo_pago');
    }
    
    // Relación: Un negocio está en un municipio
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

    // Relación: Un negocio tiene muchas reseñas
    public function resenas()
    {
        return $this->hasMany(Resena::class, 'id_negocio', 'id_negocio');
    }

    // Relación: Un negocio tiene muchas fotos adicionales
    public function fotos()
    {
        return $this->hasMany(FotoNegocio::class, 'id_negocio', 'id_negocio')->orderBy('orden');
    }

    // Relación: Un negocio tiene muchas promociones
    public function promociones()
    {
        return $this->hasMany(Promocion::class, 'id_negocio');
    }

    // Relación: Promociones vigentes
    public function promocionesVigentes()
    {
        return $this->hasMany(Promocion::class, 'id_negocio')->vigentes();
    }

    // Relación: Usuarios que tienen este negocio como favorito
    public function usuariosFavoritos()
    {
        return $this->belongsToMany(Usuario::class, 'favoritos', 'id_negocio', 'id_usuario')
            ->withTimestamps();
    }

    // Relación: Usuarios que siguen este negocio
    public function seguidores()
    {
        return $this->belongsToMany(Usuario::class, 'seguimientos', 'id_negocio', 'id_usuario')
            ->withTimestamps();
    }

    // Relación: Recomendaciones de este negocio
    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'id_negocio');
    }

    // Esto hace que Laravel agregue un campo "logo_url" automático al JSON
    protected $appends = ['logo_url'];

    // Esta función crea la URL completa automáticamente
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null; // O una imagen por defecto
    }
}
