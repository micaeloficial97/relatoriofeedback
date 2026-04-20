<?php
declare(strict_types=1);

require __DIR__.'/session.php';
require __DIR__.'/auth_mysql.php';

if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Sessao expirada. Tente novamente.';
  header('Location: ../recuperar.php');
  exit;
}

$token = (string)($_POST['token'] ?? '');
$pwd = (string)($_POST['password'] ?? '');

if ($token === '' || strlen($pwd) < 6) {
  $_SESSION['flash_error'] = 'Dados invalidos para alterar a senha.';
  header('Location: ../recuperar.php');
  exit;
}

try {
  usuario_atualizar_senha_por_token($token, $pwd);
  $_SESSION['flash_ok'] = 'Senha alterada. Faca login novamente.';
  header('Location: ../login.php');
  exit;
} catch (Throwable $e) {
  $_SESSION['flash_error'] = $e->getMessage();
  header('Location: ../recuperar.php');
  exit;
}
