<?php  include('../config.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/admin_funciones.php'); ?>
<?php 
	// Recupera los usuarios de la BD
	$admins = getAdminUsers();
	$roles = ['Admin', 'Autor'];				
?>
<?php include(ROOT_PATH . '/admin/includes/header.php'); ?>
	<title>Admin | Gestionar Usuarios</title>
</head>
<body>
	<!-- admin navbar -->
	<?php include(ROOT_PATH . '/admin/includes/navbar.php') ?>
	<div class="container content">
		<?php include(ROOT_PATH . '/admin/includes/menu.php') ?>
		<div class="action">
			<h1 class="page-title">Crear/Editar Usuario Admin</h1>

			<form method="post" action="<?php echo BASE_URL . 'admin/users.php'; ?>" >

				<!-- validar errores del formulario -->
				<?php include(ROOT_PATH . '/includes/errors.php') ?>

				<!-- si se edita el usuario, el id es requerido para identificar al usuario -->
				<?php if ($isEditingUser === true): ?>
					<input type="hidden" name="admin_id" value="<?php echo $admin_id; ?>">
				<?php endif ?>

				<input type="text" name="username" value="<?php echo $username; ?>" placeholder="Nombre de Usuario">
				<input type="email" name="email" value="<?php echo $email ?>" placeholder="Correo Electrónico">
				<input type="password" name="password" placeholder="Contraseña">
				<input type="password" name="passwordConfirmation" placeholder="Confirmar Contraseña">
				<select name="role">
					<option value="" selected disabled>Asignar Rol</option>
					<?php foreach ($roles as $key => $role): ?>
						<option value="<?php echo $role; ?>"><?php echo $role; ?></option>
					<?php endforeach ?>
				</select>

				<!-- Selecciona un boton dependiendo de si el usuario se esta editando o no -->
				<?php if ($isEditingUser === true): ?> 
					<button type="submit" class="btn" name="update_admin">Actualizar</button>
				<?php else: ?>
					<button type="submit" class="btn" name="create_admin">Guardar Usuario</button>
				<?php endif ?>
			</form>
		</div>
		
		<!-- Muestra los registros de la BD-->
		<div class="table-div">
			<!-- Muestra mensaje de notificación -->
			<?php include(ROOT_PATH . '/includes/messages.php') ?>

			<?php if (empty($admins)): ?>
				<h1>No existen administradores.</h1>
			<?php else: ?>
				<table class="table">
					<thead>
						<th>N</th>
						<th>Admin</th>
						<th>Rol</th>
						<th colspan="2">Acción</th>
					</thead>
					<tbody>
					<?php foreach ($admins as $key => $admin): ?>
						<tr>
							<td><?php echo $key + 1; ?></td>
							<td>
								<?php echo $admin['username']; ?>, &nbsp;
								<?php echo $admin['email']; ?>	
							</td>
							<td><?php echo $admin['role']; ?></td>
							<td>
								<a class="fa fa-pencil btn edit"
									href="usuarios.php?edit-admin=<?php echo $admin['id'] ?>">
								</a>
							</td>
							<td>
								<a class="fa fa-trash btn delete" 
								    href="usuarios.php?delete-admin=<?php echo $admin['id'] ?>">
								</a>
							</td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			<?php endif ?>
		</div>
		
	</div>
</body>
</html>