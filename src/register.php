<?php
$httpMethod = strtolower($_SERVER['REQUEST_METHOD']);
if ($httpMethod == 'post') {

	$name = htmlspecialchars(filter_var($_REQUEST['name']));
	$email = htmlspecialchars(filter_var($_REQUEST['email']));
	$document = htmlspecialchars(filter_var($_REQUEST['document']));
	$password = htmlspecialchars(filter_var($_REQUEST['password']));
	$birthDate = htmlspecialchars(filter_var($_REQUEST['birth-date']));
	$passwordCheck = htmlspecialchars(filter_var($_REQUEST['password-check']));

	try {
		$birthDate = new DateTime($birthDate);
	} catch (Throwable $th) {
        header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=A data informada não é valida!, corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
        die();
	}

	if ($password != $passwordCheck) {
        header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=As senhas informadas não são iguais! Corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
        die();
	}

	$passwordBCrypted = password_hash($password, PASSWORD_BCRYPT);
	$birthDate = $birthDate->format('Y-m-d');
	$expire = new DateTime();
    $expire->add(new DateInterval('PT6H'));
    $expireTime = (int)$expire->getTimestamp();
    $token = bin2hex(random_bytes(16));

    $connection = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
    );
	$state = $connection->prepare('INSERT INTO users (name, document, birth_date, email, password, token, token_expire) VALUES (?, ?, ?, ?, ?, ?, ?);');
	$state->execute([$name, $document, $birthDate, $email, $passwordBCrypted, $token, $expire->format('Y-m-d H:i:s')]);
	
    $success = setcookie('auth_token', $token, $expireTime, '/');

    header('Location: ' . $_ENV['APP_URL'] . '/home.php');
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
				<h5>Formulário de Cadastro</h5>
			</div>
			<form action="<?= $_ENV['APP_URL'] ?>/register.php" method="post">
				<div class="card-body row ">

					<div class="col-6 mb-3">
						<label for="name" class="form-label">Nome: *</label>
						<input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp" required>
						<div id="nameHelp" class="form-text"></div>
					</div>

					<div class="col-6 mb-3">
						<label for="document" class="form-label">CPF: *</label>
						<input type="text" class="form-control cpf" id="document" name="document" aria-describedby="documentHelp" required>
						<div id="documentHelp" class="form-text"></div>
					</div>

					<div class="col-6 mb-3">
						<label for="birth-date" class="form-label">Data de Nascimento: *</label>
						<input type="date" class="form-control" id="birth-date" name="birth-date" aria-describedby="birth-dateHelp" required>
						<div id="birth-dateHelp" class="form-text"></div>
					</div>

					<div class="col-6 mb-3">
						<label for="email" class="form-label">Email: *</label>
						<input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
						<div id="emailHelp" class="form-text">Informe o seu melhor e-mail</div>
					</div>

					<div class="col-6 mb-3">
						<label for="password" class="form-label">Senha: *</label>
						<input type="password" class="form-control" id="password" name="password" aria-describedby="passwordHelp" required>
						<div id="passwordHelp" class="form-text"></div>
					</div>

					<div class="col-6 mb-3">
						<label for="password-check" class="form-label">Confirmar Senha: *</label>
						<input type="password" class="form-control" id="password-check" name="password-check" aria-describedby="password-checkHelp" required>
						<div id="password-checkHelp" class="form-text"></div>
					</div>
					<div class="col-12">
						<div class="d-flex justify-content-center mb-3">
							<button class="btn btn-primary">Cadastrar</button>
						</div>
						<div class="d-flex justify-content-center mb-3">
							<a href="<?= $_ENV['APP_URL'] ?>/login.php">Já tenho cadastro</a>
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