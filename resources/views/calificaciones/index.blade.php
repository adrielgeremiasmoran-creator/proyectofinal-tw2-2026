@extends('layouts.basedashboard')

@section('titulo', 'Calificaciones - ' . $materia->nombre)

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
						<i class="bi bi-clipboard-data-fill me-2"></i>
						Calificaciones
					</h2>
					<p class="text-muted mb-0">
						<strong>Usuario:</strong> {{ $usuario->username }}
						<span class="badge bg-{{ $usuario->tipo->tipo === 'admin' ? 'danger' : ($usuario->tipo->tipo === 'profesor' ? 'success' : 'primary') }} ms-2">
							{{ ucfirst($usuario->tipo->tipo) }}
						</span>
						<br>
						<strong>Materia:</strong> {{ $materia->nombre }}
						<span class="badge bg-info ms-2">
							Promedio: {{ $promedio }}
						</span>
					</p>
				</div>
				<div>
					@if(auth()->user()->tipo->tipo === 'admin' || auth()->user()->tipo->tipo === 'profesor')
						<button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalAgregarCalificacion">
							<i class="bi bi-plus-circle-fill me-1"></i>
							Nueva Calificación
						</button>
					@endif
					<a href="{{ route('materiasxusuario.index', $usuario->id) }}" class="btn btn-secondary">
						<i class="bi bi-arrow-left me-1"></i>
						Volver a Materias
					</a>
				</div>
			</div>

			<!-- Tabla de Calificaciones -->
			<div class="card">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0">
						<i class="bi bi-list-ul me-2"></i>
						Historial de Calificaciones ({{ $calificaciones->count() }})
					</h5>
				</div>
				<div class="card-body">
					@if($calificaciones->count() > 0)
						<div class="table-responsive">
							<table class="table table-striped table-hover" id="tablaCalificaciones">
								<thead class="table-light">
									<tr>
										<th>Calificación</th>
										<th>Nivel de Aprobación</th>
										@if(auth()->user()->tipo->tipo === 'admin' || auth()->user()->tipo->tipo === 'profesor')
											<th>Acciones</th>
										@endif
									</tr>
								</thead>
								<tbody>
									@foreach($calificaciones as $calificacion)
									<tr>
										<td>
											<span class="badge bg-{{ $calificacion->calificacion >= 7 ? 'success' : ($calificacion->calificacion >= 5 ? 'warning' : 'danger') }} fs-6">
												{{ $calificacion->calificacion }}/10
											</span>
										</td>
										<td>
											@if($calificacion->calificacion >= 8)
												<span class="badge bg-success fs-6">
													<i class="bi bi-star-fill me-1"></i>
													Excelente
												</span>
											@elseif($calificacion->calificacion >= 6)
												<span class="badge bg-warning fs-6">
													<i class="bi bi-check-circle me-1"></i>
													Medio
												</span>
											@else
												<span class="badge bg-danger fs-6">
													<i class="bi bi-x-circle me-1"></i>
													Baja
												</span>
											@endif
										</td>
										@if(auth()->user()->tipo->tipo === 'admin' || auth()->user()->tipo->tipo === 'profesor')
											<td>
												<div class="btn-group" role="group">
													<!-- Botón Editar -->
													<button type="button" class="btn btn-sm btn-outline-warning"
															onclick="editarCalificacion({{ $calificacion->id }}, {{ $calificacion->calificacion }})"
															title="Editar calificación">
														<i class="bi bi-pencil-square"></i>
													</button>

													<!-- Botón Eliminar -->
													<button type="button" class="btn btn-sm btn-outline-danger"
															onclick="eliminarCalificacion({{ $calificacion->id }}, '{{ $calificacion->calificacion }}')"
															title="Eliminar calificación">
														<i class="bi bi-trash"></i>
													</button>
												</div>
											</td>
										@endif
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center py-5">
							<i class="bi bi-clipboard-data display-1 text-muted"></i>
							<h4 class="text-muted mt-3">No hay calificaciones registradas</h4>
							<p class="text-muted">Este usuario no tiene calificaciones en esta materia aún.</p>
							@if(auth()->user()->tipo->tipo === 'admin' || auth()->user()->tipo->tipo === 'profesor')
								<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarCalificacion">
									<i class="bi bi-plus-circle me-1"></i>
									Agregar Primera Calificación
								</button>
							@endif
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@if(auth()->user()->tipo->tipo === 'admin' || auth()->user()->tipo->tipo === 'profesor')
<!-- Modal para Agregar Calificación -->
<div class="modal fade" id="modalAgregarCalificacion" tabindex="-1" aria-labelledby="modalAgregarCalificacionLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h5 class="modal-title" id="modalAgregarCalificacionLabel">
					<i class="bi bi-plus-circle me-2"></i>
					Agregar Calificación
				</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="formAgregarCalificacion">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="calificacion" class="form-label fw-semibold">
							<i class="bi bi-clipboard-data text-primary me-1"></i>
							Calificación <span class="text-danger">*</span>
						</label>
						<input type="number" class="form-control" id="calificacion" name="calificacion"
							   min="0" max="10" step="0.1" placeholder="Ej: 8.5" required>
						<div class="invalid-feedback"></div>
						<div class="form-text">
							<i class="bi bi-info-circle me-1"></i>
							Ingresa una calificación del 0 al 10
						</div>
					</div>

					<!-- Preview del nivel -->
					<div class="mb-3" id="previewNivel" style="display: none;">
						<label class="form-label fw-semibold">Nivel de aprobación:</label>
						<div id="nivelPreview"></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="bi bi-x-circle me-1"></i>
						Cancelar
					</button>
					<button type="submit" class="btn btn-success" id="btnAgregar">
						<i class="bi bi-check-circle me-1"></i>
						Agregar Calificación
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal para Editar Calificación -->
<div class="modal fade" id="modalEditarCalificacion" tabindex="-1" aria-labelledby="modalEditarCalificacionLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-warning text-dark">
				<h5 class="modal-title" id="modalEditarCalificacionLabel">
					<i class="bi bi-pencil-square me-2"></i>
					Editar Calificación
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="formEditarCalificacion">
				@csrf
				@method('PUT')
				<input type="hidden" id="edit_calificacion_id" name="calificacion_id">
				<div class="modal-body">
					<div class="mb-3">
						<label for="edit_calificacion" class="form-label fw-semibold">
							<i class="bi bi-clipboard-data text-primary me-1"></i>
							Calificación <span class="text-danger">*</span>
						</label>
						<input type="number" class="form-control" id="edit_calificacion" name="calificacion"
							   min="0" max="10" step="0.1" required>
						<div class="invalid-feedback"></div>
					</div>

					<!-- Preview del nivel -->
					<div class="mb-3" id="editPreviewNivel">
						<label class="form-label fw-semibold">Nivel de aprobación:</label>
						<div id="editNivelPreview"></div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="bi bi-x-circle me-1"></i>
						Cancelar
					</button>
					<button type="submit" class="btn btn-warning" id="btnEditar">
						<i class="bi bi-check-circle me-1"></i>
						Actualizar Calificación
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="formEliminarCalificacion" method="POST" style="display:none;">
	@csrf
	@method('DELETE')
</form>
@endif
@endsection

@push('JS')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
	function cambiarEstadoBoton(tipo, cargando){
		const btn = tipo === "agregar" ? $("#btnAgregar") : $("#btnEditar");
		const texto = tipo === "agregar" ? "Agregar Calificación" : "Actualizar Calificación";
		if( cargando ){
			btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span> Guardando...');
		}
		else{
			btn.prop("disabled", false).html('<i class="bi bi-check-circle me-1"></i> ' + texto);
		}
	}

	function actualizarPreviewNivel(valor, modo){
		const contenedor = modo === "edit" ? $("#editPreviewNivel") : $("#previewNivel");
		const destino = modo === "edit" ? $("#editNivelPreview") : $("#nivelPreview");
		if( valor === "" || isNaN(valor) ){
			contenedor.slideUp();
			return;
		}
		const nota = parseFloat(valor);
		let html = "";
		if( nota >= 8 ){
			html = '<span class="badge bg-success fs-6"><i class="bi bi-star-fill me-1"></i>Excelente</span>';
		}
		else if( nota >= 6 ){
			html = '<span class="badge bg-warning fs-6"><i class="bi bi-check-circle me-1"></i>Medio</span>';
		}
		else{
			html = '<span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>Baja</span>';
		}
		destino.html(html);
		contenedor.slideDown();
	}

	function editarCalificacion(id, valor){
		$("#edit_calificacion_id").val(id);
		$("#edit_calificacion").val(valor).removeClass("is-invalid");
		actualizarPreviewNivel(valor, "edit");
		$("#modalEditarCalificacion").modal("show");
	}

	function eliminarCalificacion(id, valor){
		Swal.fire({
			icon: "warning",
			title: "¿Eliminar calificación?",
			text: "Se eliminará la calificación " + valor + ". Esta acción no se puede deshacer.",
			showCancelButton: true,
			confirmButtonColor: "#dc3545",
			confirmButtonText: "Sí, eliminar",
			cancelButtonText: "Cancelar"
		}).then((result) => {
			if( result.isConfirmed ){
				const form = document.getElementById("formEliminarCalificacion");
				form.action = "{{ url('calificaciones') }}/" + id;
				form.submit();
			}
		});
	}
</script>
@endpush

@push('JSOR')
	// Mensajes flash (eliminar)
	@if( session('success') )
		Swal.fire({ icon: "success", title: @json(session('success')), timer: 1500, showConfirmButton: false });
	@endif
	@if( session('error') )
		Swal.fire({ icon: "error", title: "Error", text: @json(session('error')) });
	@endif

	// Inicializar DataTables si hay calificaciones
	@if($calificaciones->count() > 0)
		$("#tablaCalificaciones").DataTable({
			language: {
				url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
			},
			responsive: true,
			pageLength: 10,
			lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
			order: [[0, "desc"]],
			columnDefs: [
				@if(auth()->user()->tipo->tipo === 'admin' || auth()->user()->tipo->tipo === 'profesor')
				{
					targets: [2],
					orderable: false,
					searchable: false
				}
				@endif
			]
		});
	@endif

	// Preview en tiempo real
	$("#calificacion").on("input", function(){
		$(this).removeClass("is-invalid");
		actualizarPreviewNivel($(this).val());
	});
	$("#edit_calificacion").on("input", function(){
		$(this).removeClass("is-invalid");
		actualizarPreviewNivel($(this).val(), "edit");
	});

	// Agregar
	$("#formAgregarCalificacion").on("submit", function(e){
		e.preventDefault();
		cambiarEstadoBoton("agregar", true);
		$.ajax({
			url: `{{ route('calificaciones.store', [$usuario->id, $materia->id]) }}`,
			method: "POST",
			data: $(this).serialize(),
			success: function(response){
				$("#modalAgregarCalificacion").modal("hide");
				Swal.fire({
					icon: "success",
					title: "¡Calificación agregada!",
					text: "La calificación se ha agregado correctamente",
					timer: 1500,
					showConfirmButton: false,
					timerProgressBar: true
				}).then(() => {
					location.reload();
				});
			},
			error: function(xhr){
				cambiarEstadoBoton("agregar", false);
				if( xhr.status === 422 ){
					const errors = xhr.responseJSON.errors;
					Object.keys(errors).forEach(function(key){
						$(`#${key}`).addClass("is-invalid");
						$(`#${key}`).siblings(".invalid-feedback").text(errors[key][0]);
					});
				}
				else{
					Swal.fire({
						icon: "error",
						title: "Error",
						text: xhr.responseJSON?.message || "Ocurrió un problema al agregar la calificación",
						confirmButtonText: "Aceptar"
					});
				}
			}
		});
	});

	// Editar
	$("#formEditarCalificacion").on("submit", function(e){
		e.preventDefault();
		const id = $("#edit_calificacion_id").val();
		cambiarEstadoBoton("editar", true);
		$.ajax({
			url: `{{ url('calificaciones') }}/${id}`,
			method: "POST",
			data: $(this).serialize(),
			success: function(response){
				$("#modalEditarCalificacion").modal("hide");
				Swal.fire({
					icon: "success",
					title: "¡Calificación actualizada!",
					text: "La calificación se ha actualizado correctamente",
					timer: 1500,
					showConfirmButton: false,
					timerProgressBar: true
				}).then(() => {
					location.reload();
				});
			},
			error: function(xhr){
				cambiarEstadoBoton("editar", false);
				if( xhr.status === 422 ){
					const errors = xhr.responseJSON.errors;
					Object.keys(errors).forEach(function(key){
						const field = key === "calificacion" ? "edit_calificacion" : `edit_${key}`;
						$(`#${field}`).addClass("is-invalid");
						$(`#${field}`).siblings(".invalid-feedback").text(errors[key][0]);
					});
				}
				else{
					Swal.fire({
						icon: "error",
						title: "Error",
						text: xhr.responseJSON?.message || "Ocurrió un problema al actualizar la calificación",
						confirmButtonText: "Aceptar"
					});
				}
			}
		});
	});

	// Limpiar al cerrar modales
	$(".modal").on("hidden.bs.modal", function(){
		$(this).find("form")[0].reset();
		$(this).find(".is-invalid").removeClass("is-invalid");
		$(this).find(".invalid-feedback").text("");
		$("#previewNivel, #editPreviewNivel").slideUp();
		cambiarEstadoBoton("agregar", false);
		cambiarEstadoBoton("editar", false);
	});

	console.log("Vista calificaciones con modales cargada");
@endpush