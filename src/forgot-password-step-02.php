<?php
$httpMethod = strtolower($_SERVER['REQUEST_METHOD']);
if ($httpMethod == 'post') {

	$email = htmlspecialchars(filter_var($_REQUEST['email']));

    if ($email == null) {
        header('Location: ' . $_ENV['APP_URL'] . '/forgot-password-step-01.php?error=O e-mail informado não é valido! Corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
        die();
    }

    $connection = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
    );
    $datetime = new DateTime();

    $state = $connection->prepare('SELECT id,name, document, birth_date, email, password FROM users WHERE email = ? LIMIT 1;');
    $state->execute([$email]);
    $user = $state->fetch();

    if (!$user) {
        header('Location: ' . $_ENV['APP_URL'] . '/forgot-password-step-01.php?error=Usuário não encontrado!, verifique o e-mail e tente novamente.');
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Formulário de Atualização de Senha</title>
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
				<h5>Formulário de Atualização de Senha</h5>
			</div>
			<form action="<?= $_ENV['APP_URL'] ?>/update-password-by-id.php" method="post">
				<input name="id" value="<?= $user->id ?>" hidden />
				<div class="card-body row ">

					<div class="col-6 mb-3">
						<label for="new_password" class="form-label">Nova Senha: *</label>
						<input type="password" class="form-control" id="new_password" name="new_password" aria-describedby="new_passwordHelp" required>
						<div id="new_passwordHelp" class="form-text"></div>
					</div>

					<div class="col-6 mb-3">
						<label for="confirm_new_password" class="form-label">Confirme a Nova Senha: *</label>
						<input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" aria-describedby="confirm_new_passwordHelp" required>
						<div id="confirm_new_passwordHelp" class="form-text"></div>
					</div>

					<div class="col-12">
						<div class="d-flex justify-content-center mb-3">
							<button class="btn btn-primary">Salvar</button>
						</div>
						<div class="d-flex justify-content-center mb-3">
							<a href="<?= $_ENV['APP_URL'] ?>/home">Cancelar</a>
						</div>
					</div>
			</form>
		</div>
	</div>
	</div>
	<script src="<?= $_ENV['APP_URL'] ?>/assets/js/bootstrap@5.3.2.js"></script>
</body>

</html>