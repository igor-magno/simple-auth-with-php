<?php
$httpMethod = strtolower($_SERVER['REQUEST_METHOD']);
if ($httpMethod == 'post') {

    $email = htmlspecialchars(filter_var($_REQUEST['email']));
    $password = htmlspecialchars(filter_var($_REQUEST['password']));

    if ($email == null) {
        header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=O e-mail informado não é valido! Corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
        die();
    }
    if ($password == null) {
        header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=A senha informada não é valida! Corrija e tente novamente. Caso o problema persista entre em contato com o suporte.');
        die();
    }

    $connection = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';port=' . $_ENV['DB_PORT'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
    );
    $datetime = new DateTime();

    $state = $connection->prepare('SELECT id,name, document, birth_date, email, password FROM users WHERE email = ? AND token_expire > ?  LIMIT 1;');
    $state->execute([$email, $datetime->format('Y-m-d H:i:s')]);
    $user = $state->fetch();

    if (!$user) {
        header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=O e-mail ou a senha estão incorretos!, verifique-os e tente novamente.');
        die();
    }

    if (!password_verify($password, $user->password)) {
        header('Location: ' . $_ENV['APP_URL'] . '/login.php?error=O e-mail ou a senha estão incorretos!, verifique-os e tente novamente.');
        die();
    }

    $expire = new DateTime();
    $expire->add(new DateInterval('PT6H'));
    $expireTime = (int)$expire->getTimestamp();
    $token = bin2hex(random_bytes(16));

    $state = $connection->prepare('UPDATE users SET token = ?, token_expire = ? WHERE id = ?;');
    $state->execute([$token, $expire->format('Y-m-d H:i:s'), $user->id]);

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
    <title>Login</title>
    <link href="<?= $_ENV['APP_URL'] ?>/assets/css/bootstrap@5.3.2.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div style="width: 25rem;">
            <?php require __DIR__ . '/error-alert.php' ?>
            <?php require __DIR__ . '/success-alert.php' ?>
        </div>
        <div class="card" style="width: 25rem;">
            <div class="card-header">
                <h5>Login</h5>
            </div>
            <form action="<?= $_ENV['APP_URL'] ?>/login.php" method="post">
                <div class="card-body row ">

                    <div class="col-12 mb-3">
                        <label for="email" class="form-label">Email: *</label>
                        <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
                        <div id="emailHelp" class="form-text">Esse deve ser o e-mail informado no cadastro.</div>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="password" class="form-label">Senha: *</label>
                        <input type="password" class="form-control" id="password" name="password" aria-describedby="passwordHelp" required>
                        <div id="passwordHelp" class="form-text"></div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-center mb-3">
                            <button class="btn btn-primary">Entrar</button>
                        </div>
                        <div class="d-flex justify-content-center mb-3">
                            <a href="<?= $_ENV['APP_URL'] ?>/register.php">Cadastre-se</a>
                        </div>
                        <div class="d-flex justify-content-center mb-3">
                            <a href="<?= $_ENV['APP_URL'] ?>/forgot-password-step-01.php">Esqueceu sua senha?</a>
                        </div>
                    </div>
            </form>
        </div>
    </div>
    </div>
    <script src="<?= $_ENV['APP_URL'] ?>/assets/js/bootstrap@5.3.2.js"></script>
</body>

</html>