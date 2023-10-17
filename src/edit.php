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

	$id = htmlspecialchars(filter_var($_REQUEST['id']));
	$name = htmlspecialchars(filter_var($_REQUEST['name']));
	$email = htmlspecialchars(filter_var($_REQUEST['email']));
	$document = htmlspecialchars(filter_var($_REQUEST['document']));
	$birthDate = htmlspecialchars(filter_var($_REQUEST['birth-date']));

	try {
		$birthDate = new DateTime($birthDate);
	} catch (Throwable $th) {
        header('Location: ' . $_ENV['APP_URL'] . '/edit.php?id='.$user->id.'&error=A data informada não é valida!, corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
        die();
	}

    $connection = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
    );
	$state = $connection->prepare('UPDATE users SET name = ?, document = ?, birth_date = ?, email = ? WHERE id = ?;');
	$state->execute([$name, $document, $birthDate->format('Y-m-d'), $email, $id]);

    header('Location: ' . $_ENV['APP_URL'] . '/home.php?success=Dados atualizados com sucesso.');
    die();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Formulário de Atualização dos Dados</title>
	<link href="<?= $_ENV['APP_URL'] ?>/assets/css/bootstrap@5.3.2.css" rel="stylesheet">
	<script src="<?= $_ENV['APP_URL'] ?>/assets/js/jquery@3.7.1.js"></script>
	<script src="<?= $_ENV['APP_URL'] ?>/assets/js/mask@1.14.16.js"></script>
</head>

<body>
	<div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
		<div style="width: 50rem;">
			<?php require __DIR__ . '/error-alert.php' ?>
            <?php require __DIR__ . '/success-alert.php' ?>
		</div>
		<div class="card" style="width: 50rem;">
			<div class="card-header">
				<h5>Formulário de Atualização dos Dados</h5>
			</div>
			<form action="<?= $_ENV['APP_URL'] ?>/edit.php?id=<?= $user->id ?>" method="post">
				<div class="card-body row ">

					<div class="col-6 mb-3">
						<label for="name" class="form-label">Nome: *</label>
						<input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp" required value="<?= $user->name ?>">
						<div id="nameHelp" class="form-text"></div>
					</div>

					<div class="col-6 mb-3">
						<label for="document" class="form-label">CPF: *</label>
						<input type="text" class="form-control cpf" id="document" name="document" aria-describedby="documentHelp" required value="<?= $user->document ?>">
						<div id="documentHelp" class="form-text"></div>
					</div>

					<div class="col-6 mb-3">
						<label for="birth-date" class="form-label">Data de Nascimento: *</label>
						<input type="date" class="form-control" id="birth-date" name="birth-date" aria-describedby="birth-dateHelp" required value="<?= (new DateTime($user->birth_date))->format('Y-m-d') ?>">
						<div id="birth-dateHelp" class="form-text"></div>
					</div>

					<div class="col-6 mb-3">
						<label for="email" class="form-label">Email: *</label>
						<input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required value="<?= $user->email ?>">
						<div id="emailHelp" class="form-text">Informe o seu melhor e-mail</div>
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
	<script>
		$(document).ready(function() {
			$('.cpf').mask('000.000.000-00', {
				reverse: true
			});
		})
	</script>
</body>

</html>