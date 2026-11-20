<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatosDemoSeeder extends Seeder
{
	public function run(): void
	{
		$profesorTipo = DB::table('tipos')->where('tipo', 'profesor')->first()->id;
		$estudianteTipo = DB::table('tipos')->where('tipo', 'estudiante')->first()->id;

		DB::table('users')->insert([
			'tipos_id' => $profesorTipo,
			'username' => 'Profe1',
			'email' => 'profe1@sistema.com',
			'password' => Hash::make('profe123'),
		]);

		$estudiante1Id = DB::table('users')->insertGetId([
			'tipos_id' => $estudianteTipo,
			'username' => 'Estudiante1',
			'email' => 'estudiante1@sistema.com',
			'password' => Hash::make('estudiante123'),
		]);

		$estudiante2Id = DB::table('users')->insertGetId([
			'tipos_id' => $estudianteTipo,
			'username' => 'Estudiante2',
			'email' => 'estudiante2@sistema.com',
			'password' => Hash::make('estudiante123'),
		]);

		$matematicasId = DB::table('materias')->insertGetId(['nombre' => 'Matemáticas']);
		$programacionId = DB::table('materias')->insertGetId(['nombre' => 'Programación']);
		$inglesId = DB::table('materias')->insertGetId(['nombre' => 'Inglés']);

		$asignacion1 = DB::table('materias_x_usuarios')->insertGetId(['users_id' => $estudiante1Id, 'materias_id' => $matematicasId]);
		$asignacion2 = DB::table('materias_x_usuarios')->insertGetId(['users_id' => $estudiante1Id, 'materias_id' => $programacionId]);
		$asignacion3 = DB::table('materias_x_usuarios')->insertGetId(['users_id' => $estudiante2Id, 'materias_id' => $inglesId]);

		DB::table('calificaciones')->insert([
			['materias_x_usuarios_id' => $asignacion1, 'calificacion' => 8.5],
			['materias_x_usuarios_id' => $asignacion1, 'calificacion' => 6],
			['materias_x_usuarios_id' => $asignacion1, 'calificacion' => 9.25],
			['materias_x_usuarios_id' => $asignacion2, 'calificacion' => 7.75],
			['materias_x_usuarios_id' => $asignacion2, 'calificacion' => 9],
			['materias_x_usuarios_id' => $asignacion3, 'calificacion' => 8],
		]);
	}
}