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

    if ($id == null) {
        header('Location: ' . $_ENV['APP_URL'] . '/remove.php?error=O identificador do usuário informado não é valida! Corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
        die();
    }

    if ($user->id != $id) {
        header('Location: ' . $_ENV['APP_URL'] . '/remove.php?error=Você tentou excluir os dados de outro usuário e essa ação não é permitida! Corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
        die();
    }

    $connection = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
    );
    $state = $connection->prepare('DELETE FROM users WHERE id = ?;');
    $state->execute([$id]);

    setcookie('auth_token', '', time() - 3600, '/');

    header('Location: ' . $_ENV['APP_URL'] . '/login.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Exclusão de Dados</title>
	<link href="<?= $_ENV['APP_URL'] ?>/assets/css/bootstrap@5.3.2.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div style="width: 50rem;">
            <?php require __DIR__ . '/error-alert.php' ?>
            <?php require __DIR__ . '/success-alert.php' ?>
        </div>
        <div class="card border border-danger" style="width: 50rem;">
            <div class="card-header border-bottom border-danger">
                <h5 class="text-danger">Solicitar Exclusão de Dados</h5>
            </div>
            <form action="<?= $_ENV['APP_URL'] ?>/remove.php" method="post">
                <input name="id" value="<?= $user->id ?>" hidden />
                <div class="card-body row ">
                    <div class="col-12 mb-3">
                        <p class="text-danger">Após confirmar a exclusão não será possível recuperar os seus dados!</p>
                        <p class="text-danger">Prossiga por conta risco.</p>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-center mb-3">
                            <button class="btn btn-danger">Confirmar Exclusão</button>
                        </div>
                        <div class="d-flex justify-content-center mb-3">
                            <a href="<?= $_ENV['APP_URL'] ?>/home.php">Voltar</a>
                        </div>
                    </div>
            </form>
        </div>
    </div>
    </div>
	<script src="<?= $_ENV['APP_URL'] ?>/assets/js/bootstrap@5.3.2.js"></script>
</body>

</html>