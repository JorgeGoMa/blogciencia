<!-- Identifica el tipo de usuario y selecciona el menú correspondiente a su rol   -->
<?php if(isset($_SESSION['user']['role'])){
				switch ($_SESSION['user']['role']) {
					case 'Admin': ?>
						<div class="menu">
							<div class="card">
								<div class="card-header">
									<h2>Acciones</h2>
								</div>
								<div class="card-content">
									<a href="<?php echo BASE_URL . 'admin/create_post.php' ?>">Crear Artículo</a>
									<a href="<?php echo BASE_URL . 'admin/posts.php' ?>">Administrar Artículos</a>
									<a href="<?php echo BASE_URL . 'admin/users.php' ?>">Administrar Usuarios</a>
									<a href="<?php echo BASE_URL . 'admin/topics.php' ?>">Administrar Temas</a>
								</div>
							</div>
						</div>
					<?php break;
					case 'Autor': ?>
						<div class="menu">
							<div class="card">
								<div class="card-header">
									<h2>Acciones</h2>
								</div>
								<div class="card-content">
									<a href="<?php echo BASE_URL . 'admin/create_post.php' ?>">Crear Artículo</a>
									<a href="<?php echo BASE_URL . 'admin/posts.php' ?>">Administrar Artículos</a>
									<a href="<?php echo BASE_URL . 'admin/topics.php' ?>">Administrar Temas</a>
								</div>
							</div>
						</div>
												
					<?php break;					
				}
			} ?>	