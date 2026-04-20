<?php

require __DIR__ . '/session.php';
require __DIR__ . '/auth_mysql.php';

function registrar_log($message)
{
  @file_put_contents(__DIR__ . '/app_error.log', '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  http_response_code(405);
  exit('Metodo nao permitido');
}

if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Sessao expirada. Tente novamente.';
  header('Location: ../registrar.php');
  exit;
}

$name  = trim((string)($_POST['name'] ?? ''));
$email = strtolower(trim((string)($_POST['email'] ?? '')));
$pass  = (string)($_POST['password'] ?? '');

if ($name === '' || strlen($name) > 120) {
  $_SESSION['flash_error'] = 'Informe um nome valido.';
  header('Location: ../registrar.php');
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
  $_SESSION['flash_error'] = 'Informe um e-mail valido.';
  header('Location: ../registrar.php');
  exit;
}

if (strlen($pass) < 6) {
  $_SESSION['flash_error'] = 'A senha deve ter pelo menos 6 caracteres.';
  header('Location: ../registrar.php');
  exit;
}

try {
  if (usuario_por_email($email)) {
    $_SESSION['flash_error'] = 'Este e-mail ja esta cadastrado. Faca login ou use "Esqueceu a senha?".';
    header('Location: ../registrar.php');
    exit;
  }

  usuario_criar($name, $email, $pass);
  $_SESSION['flash_ok'] = 'Cadastro realizado. Voce ja pode acessar sua conta.';
  header('Location: ../login.php');
  exit;
} catch (mysqli_sql_exception $e) {
  registrar_log('register.php SQL error: ' . $e->getMessage());
  error_log('register.php SQL error: ' . $e->getMessage());

  if ((int) $e->getCode() === 1062) {
    $_SESSION['flash_error'] = 'Este e-mail ja esta cadastrado. Faca login ou use "Esqueceu a senha?".';
  } elseif (app_debug()) {
    $_SESSION['flash_error'] = 'Erro SQL: ' . $e->getMessage();
  } else {
    $_SESSION['flash_error'] = 'Nao foi possivel criar a conta.';
  }

  header('Location: ../registrar.php');
  exit;
} catch (Exception $e) {
  registrar_log('register.php error: ' . $e->getMessage());
  error_log('register.php error: ' . $e->getMessage());

  $_SESSION['flash_error'] = app_debug()
    ? 'Erro: ' . $e->getMessage()
    : 'Nao foi possivel criar a conta.';
  header('Location: ../registrar.php');
  exit;
}
