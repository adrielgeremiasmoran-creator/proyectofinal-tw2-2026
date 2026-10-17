<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
	public $timestamps = false;

	protected $fillable = [
		'nombre'
	];

	// Relación: Una materia puede tener muchas relaciones materias_x_usuarios
	public function materiasXUsuarios(){
		return $this->hasMany(MateriasXUsuario::class, 'materias_id');
	}

	// Relación: Una materia puede tener muchos usuarios inscritos (many-to-many)
	public function users(){
		return $this->belongsToMany(User::class, 'materias_x_usuarios', 'materias_id', 'users_id');
	}
}