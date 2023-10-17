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

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Formulário de Cadastro</title>
	<link href="<?= $_ENV['APP_URL'] ?>/assets/css/bootstrap@5.3.2.css" rel="stylesheet">
</head>

<body>
	<div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
		<div style="width: 50rem;">
			<?php require __DIR__ . '/error-alert.php' ?>
			<?php require __DIR__ . '/success-alert.php' ?>
		</div>
		<div class="card" style="width: 50rem;">
			<div class="card-body">
				<div class="mb-3">
					<p>Olá <strong><?= $user->name ?></strong></p>
					<p>E-mail: <?= $user->email ?></p>
					<p>CPF: <?= $user->document ?></p>
					<p>Data de Nascimento: <?= (new DateTime($user->birth_date))->format('d/m/Y') ?></p>
				</div>
				<div class="d-flex justify-content-center mb-3">
					<a href="<?= $_ENV['APP_URL'] ?>/edit.php?id=<?= $user->id ?>">Quer atualizar seus dados clique aqui</a>
				</div>
				<div class="d-flex justify-content-center mb-3">
					<a href="<?= $_ENV['APP_URL'] ?>/update-password.php?id=<?= $user->id ?>">Quer atualizar sua senha clique aqui</a>
				</div>
				<div class="d-flex justify-content-center mb-3">
					<a href="<?= $_ENV['APP_URL'] ?>/remove.php">Se deseja excluir seus dados clique aqui</a>
				</div>
				<div class="d-flex justify-content-center mb-3">
					<a href="<?= $_ENV['APP_URL'] ?>/logout.php">Sair</a>
				</div>
			</div>
		</div>
	</div>
	<script src="<?= $_ENV['APP_URL'] ?>/assets/js/bootstrap@5.3.2.js"></script>
</body>

</html>