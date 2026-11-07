<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccesoController extends Controller
{
	public function mostrarFormulario(){
		if( Auth::check() ){
			return $this->redirigirSegunTipo();
		}
		return view('auth.acceso');
	}
	public function iniciarSesion(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email|max:255',
			'password' => 'required|string|min:6'
		],[
			'email.required' => 'El correo electrónico es obligatorio',
			'email.email' => 'Debe ingresar un correo electrónico válido',
			'email.max' => 'El correo electrónico no puede tener más de 255 caracteres',
			'password.required' => 'La contraseña es obligatoria',
			'password.string' => 'La contraseña debe ser una cadena de texto',
			'password.min' => 'La contraseña debe tener al menos 6 caractéres',
		]);
		if( $validator->fails() ){
			if( $request->ajax() ){
				return response()->json([
					'success' => false,
					'errors' => $validator->errors()
				], 422);
			}
			return redirect()
				->back()
				->withErrors( $validator )
				->withInput( $request->only('email') );
		}
		$credenciales = $request->only('email', 'password');
		if( Auth::attempt( $credenciales ) ){
			$request->session()->regenerate();
			$usuario = Auth::user();
			if( !$usuario->tipo ){
				Auth::logout();
				if( $request->ajax() ){
					return response()->json([
						'success' => false,
						'message' => 'El usuario no tiene un tipo asignado.'
					], 401);
				}
				return redirect()
					->route('acceso')
					->withErrors(['email' => 'El usuario no tiene un tipo asignado.']);
			}
			if( $request->ajax() ){
				return response()->json([
					'success' => true,
					'message' => 'Inicio de sesión exitoso',
					'redirect' => $this->obtenerUrlRedireccion()
				]);
			}
			return $this->redirigirSegunTipo();
		}
		if( $request->ajax() ){
			return response()->json([
				'success' => false,
				'message' => 'Las credenciales proporcionadas son incorrectas.'
			], 401);
		}
		return redirect()
			->back()
			->withErrors(['email' => 'Las credenciales proporcionadas son incorrectas.'])
			->withInput($request->only('email'));
	}
	public function cerrarSesion(Request $request){
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect()->route('acceso');
	}
	private function redirigirSegunTipo(){
		$usuario = Auth::user();
		$tipo = $usuario->tipo->tipo;
		switch( $tipo ){
			case 'admin':
			case 'profesor':
				return redirect()->route('usuarios.index');
			case 'estudiante':
				return redirect()->route('materiasxusuario.index', $usuario->id);
			default:
				Auth::logout();
				return redirect()
					->route('acceso')
					->withErrors(['email' => 'Tipo de usuario no reconocido.']);
		}
	}
	private function obtenerUrlRedireccion(){
		$usuario = Auth::user();
		$tipo = $usuario->tipo->tipo;
		switch( $tipo ){
			case 'admin':
			case 'profesor':
				return route('usuarios.index');
			case 'estudiante':
				return route('materiasxusuario.index', $usuario->id);
			default:
				return route('acceso');
		}
	}
}