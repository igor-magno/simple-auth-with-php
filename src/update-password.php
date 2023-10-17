<?php

try {
	if (isset($_COOKIE['auth_token'])) {
		$token = $_COOKIE['auth_token'];
	} else {
		header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=Sua seção expirou!, realize o login novamente.');
		die();
	}

	$connection = new PDO(
		'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'],
		$_ENV['DB_USER'],
		$_ENV['DB_PASSWORD'],
		[PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
	);
	$datetime = new DateTime();
	$state = $connection->prepare('SELECT id, name, document, birth_date, email, password FROM users WHERE token = ? AND token_expire > ? LIMIT 1;');
	$state->execute([$token, $datetime->format('Y-m-d H:i:s')]);
	$user = $state->fetch();

	if (!$user) {
		header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=Sua seção expirou!, realize o login novamente.');
		die();
	}
} catch (Throwable $th) {
	header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=' . $th->getCode() . ' - ' . $th->getMessage());
	die();
}

$httpMethod = strtolower($_SERVER['REQUEST_METHOD']);
if ($httpMethod == 'post') {

	$password = htmlspecialchars(filter_var($_REQUEST['password']));
	$newPassword = htmlspecialchars(filter_var($_REQUEST['new_password']));
	$confirmNewPassword = htmlspecialchars(filter_var($_REQUEST['confirm_new_password']));

	if (!password_verify($password, $user->password)) {
		header('Location: ' . $_ENV['APP_URL'] . '/update-password.php?error=A senha atual não está correta!, caso não lembre sua senha você pode solicitar a redefinição por e-mail. Caso o problema persista entre em contato com o suporte.');
		die();
	}
	if ($newPassword !== $confirmNewPassword) {
		header('Location: ' . $_ENV['APP_URL'] . '/update-password.php?error=A nova senha e a confirmação da nova senha não são iguais!, corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
		die();
	}

	$newPassword = password_hash($newPassword, PASSWORD_BCRYPT);

	$state = $connection->prepare('UPDATE users SET password = ? WHERE id = ?;');
	$state->execute([$newPassword, $user->id]);

	header('Location: ' . $_ENV['APP_URL'] . '/home.php?success=Dados atualizados com sucesso.');
	die();
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
			<form action="<?= $_ENV['APP_URL'] ?>/update-password.php?id=<?= $user->id ?>" method="post">
				<div class="card-body row ">

					<div class="col-12 mb-3">
						<label for="password" class="form-label">Senha Atual: *</label>
						<input type="password" class="form-control" id="password" name="password" aria-describedby="passwordHelp" required>
						<div id="passwordHelp" class="form-text"></div>
					</div>

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
							<a href="<?= $_ENV['APP_URL'] ?>/home.php">Cancelar</a>
						</div>
					</div>
			</form>
		</div>
	</div>
	</div>
	<script src="<?= $_ENV['APP_URL'] ?>/assets/js/bootstrap@5.3.2.js"></script>
</body>

</html>