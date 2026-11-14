@extends('layouts.basedashboard')

@section('titulo', 'Gestión de Usuarios')

@push('CSS')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-people-fill me-2"></i>
                        Gestión de Usuarios
                    </h2>
                    <p class="text-muted mb-0">Administra los usuarios del sistema</p>
                </div>
                <div>
                    <a href="{{ route('usuarios.create') }}" class="btn btn-success">
                        <i class="bi bi-person-plus-fill me-1"></i>
                        Nuevo Usuario
                    </a>
                </div>
            </div>

            <!-- Tabla de Usuarios -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaUsuarios">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->id }}</td>
                                    <td>{{ $usuario->username }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        @if($usuario->tipo->tipo === 'admin')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-shield-fill-check me-1"></i>
                                                Administrador
                                            </span>
                                        @elseif($usuario->tipo->tipo === 'profesor')
                                            <span class="badge bg-success">
                                                <i class="bi bi-person-workspace me-1"></i>
                                                Profesor
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="bi bi-mortarboard me-1"></i>
                                                Estudiante
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Botón Editar -->
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="editarUsuario({{ $usuario->id }})"
                                                    title="Editar usuario">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- Botón Eliminar -->
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="eliminarUsuario({{ $usuario->id }}, '{{ $usuario->username }}')"
                                                    title="Eliminar usuario">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            <!-- Botón Materias (Solo admin y profesores pueden gestionar) -->
                                            @if($usuario->tipo->tipo === 'estudiante')
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="gestionarMaterias({{ $usuario->id }})"
                                                    title="Gestionar materias">
                                                <i class="bi bi-book"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('JS')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
	// Ir al formulario de edición del usuario
	function editarUsuario(id){
		window.location.href = "{{ url('usuarios') }}/" + id + "/edit";
	}

	// Eliminar usuario con confirmación
	function eliminarUsuario(id, username){
		const enviarFormulario = function(){
			const form = document.createElement('form');
			form.method = 'POST';
			form.action = "{{ url('usuarios') }}/" + id;
			form.innerHTML =
				'<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
				'<input type="hidden" name="_method" value="DELETE">';
			document.body.appendChild(form);
			form.submit();
		};

		if( typeof Swal !== 'undefined' ){
			Swal.fire({
				title: '¿Eliminar usuario?',
				text: 'Se eliminará al usuario "' + username + '". Esta acción no se puede deshacer.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#dc3545',
				cancelButtonColor: '#6c757d',
				confirmButtonText: 'Sí, eliminar',
				cancelButtonText: 'Cancelar'
			}).then(function(result){
				if( result.isConfirmed ){
					enviarFormulario();
				}
			});
		} else if( confirm('¿Eliminar al usuario "' + username + '"?') ){
			enviarFormulario();
		}
	}

	// Gestionar materias del estudiante (Clase 5)
	function gestionarMaterias(id){
		window.location.href = "{{ url('materiasxusuario') }}/" + id;
	}
</script>
@endpush

@push('JSOR')
// Inicializar DataTables
$("#tablaUsuarios").DataTable({
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    order: [[0, "asc"]],
    columnDefs: [
        {
            targets: [4], // Columna de acciones
            orderable: false,
            searchable: false
        }
    ]
});

console.log("DataTables inicializado correctamente");
@endpush