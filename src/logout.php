<?php

setcookie('auth_token', '', time() - 3600, '/');
header('Location: ' . $_ENV['APP_URL'] . '/login.php');
die();