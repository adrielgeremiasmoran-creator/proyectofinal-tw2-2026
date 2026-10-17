<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	use HasFactory, Notifiable;

	public $timestamps = false;

	protected $fillable = [
		'tipos_id',
		'username',
		'email',
		'password',
	];

	protected $hidden = [
		'password',
	];

	// Relación: Un usuario pertenece a un tipo
	public function tipo(){
		return $this->belongsTo(Tipo::class, 'tipos_id');
	}

	// Relación: Un usuario puede tener muchas asignaciones materias_x_usuarios
	public function materiasXUsuarios(){
		return $this->hasMany(MateriasXUsuario::class, 'users_id');
	}

	// Relación: Un usuario puede tener muchas materias (many-to-many)
	public function materias(){
		return $this->belongsToMany(Materia::class, 'materias_x_usuarios', 'users_id', 'materias_id');
	}
}