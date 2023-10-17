<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Formulário de Solicitação de Atualização de Senha</title>
	<link href="<?= $_ENV['APP_URL'] ?>/assets/css/bootstrap@5.3.2.css" rel="stylesheet">
</head>

<body>
	<div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
		<div style="width: 50rem;">
			<?php require __DIR__ . '/error-alert.php' ?>
            <?php require __DIR__ . '/success-alert.php' ?>
		</div>
		<div class="card" style="width: 50rem;">
			<div class="card-header">
				<h5>Formulário de Solicitação de Atualização de Senha</h5>
			</div>
			<form action="<?= $_ENV['APP_URL'] ?>/forgot-password-step-02.php" method="post">
				<div class="card-body row ">

					<div class="col-12 mb-3">
						<label for="email" class="form-label">E-mail: *</label>
						<input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
						<div id="emailHelp" class="form-text"></div>
					</div>

					<div class="col-12">
						<div class="d-flex justify-content-center mb-3">
							<button class="btn btn-primary">Solicitar</button>
						</div>
						<div class="d-flex justify-content-center mb-3">
							<a href="<?= $_ENV['APP_URL'] ?>/login.php">Cancelar</a>
						</div>
					</div>
			</form>
		</div>
	</div>
	</div>
	<script src="<?= $_ENV['APP_URL'] ?>/assets/js/bootstrap@5.3.2.js"></script>
</body>

</html>