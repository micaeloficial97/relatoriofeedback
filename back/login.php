<?php
declare(strict_types=1);
require __DIR__.'/session.php';
require __DIR__.'/auth_mysql.php';

if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
  $_SESSION['flash_error'] = 'Sessão expirada. Tente novamente.';
  header('Location: ../login.php'); exit;
}

$email = trim($_POST['email'] ?? '');
$pass  = (string)($_POST['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') {
  $_SESSION['flash_error'] = 'Dados inválidos.'; header('Location: ../login.php'); exit;
}

try {
  $usuario = usuario_autenticar(strtolower($email), $pass);

  session_regenerate_id(true);
  $_SESSION['uid'] = (int) $usuario['id'];
  $_SESSION['nome'] = $usuario['nome'] ?? null;
  $_SESSION['email'] = $usuario['email'] ?? $email;
  header('Location: ../app/index.php'); exit;
} catch (Throwable $e) {
  $_SESSION['flash_error'] = $e->getMessage();
  
  header('Location: ../login.php'); exit;
}
