<?php

$httpMethod = strtolower($_SERVER['REQUEST_METHOD']);
if ($httpMethod == 'post') {

	$id = htmlspecialchars(filter_var($_REQUEST['id']));
	$newPassword = htmlspecialchars(filter_var($_REQUEST['new_password']));
	$confirmNewPassword = htmlspecialchars(filter_var($_REQUEST['confirm_new_password']));

	if ($newPassword !== $confirmNewPassword) {
		header('Location: ' . $_ENV['APP_URL'] . '/forgot-password-step-01.php?error=A nova senha e a confirmação da nova senha não são iguais!, corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
		die();
	}

	$newPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $connection = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
    );
	$state = $connection->prepare('UPDATE users SET password = ? WHERE id = ?;');
	$state->execute([$newPassword, $id]);

	header('Location: ' . $_ENV['APP_URL'] . '/login.php?success=Senha atualizada com sucesso, agora realize o login com sua nova senha.');
	die();
}