<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    protected $table = 'publicaciones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'archivo_path',
        'archivo_directorio',
        'proyecto_id',
        'user_id',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suppImages()
    {
        return $this->hasMany(SuppImage::class);
    }
}
