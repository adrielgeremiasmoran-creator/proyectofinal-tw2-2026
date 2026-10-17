<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriasXUsuario extends Model
{
	public $timestamps = false;
	protected $table = 'materias_x_usuarios';
	protected $fillable = [
		'materias_id',
		'users_id'
	];

	// Relación: La asignación pertenece a una materia
	public function materia(){
		return $this->belongsTo(Materia::class, 'materias_id');
	}

	// Relación: La asignación pertenece a un usuario
	public function user(){
		return $this->belongsTo(User::class, 'users_id');
	}

	// Relación: Una asignación puede tener muchas calificaciones
	public function calificaciones(){
		return $this->hasMany(Calificacion::class, 'materias_x_usuarios_id');
	}
}