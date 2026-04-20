<?php

require_once __DIR__ . '/db.php';

function usuario_por_email($email)
{
  $mysqli = db_connection();
  $sql = 'SELECT id, nome, email, password_hash, ativo FROM relatorio_usuarios WHERE email = ? LIMIT 1';
  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param('s', $email);
  $stmt->execute();

  $stmt->bind_result($id, $nome, $emailEncontrado, $passwordHash, $ativo);
  if (!$stmt->fetch()) {
    return null;
  }

  return [
    'id' => $id,
    'nome' => $nome,
    'email' => $emailEncontrado,
    'password_hash' => $passwordHash,
    'ativo' => $ativo,
  ];
}

function usuario_criar($nome, $email, $senha)
{
  $mysqli = db_connection();
  $hash = password_hash($senha, PASSWORD_DEFAULT);

  $sql = 'INSERT INTO relatorio_usuarios (nome, email, password_hash) VALUES (?, ?, ?)';
  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param('sss', $nome, $email, $hash);
  $stmt->execute();

  return (int) $mysqli->insert_id;
}

function usuario_autenticar($email, $senha)
{
  $usuario = usuario_por_email($email);

  if (!$usuario || (int) $usuario['ativo'] !== 1 || !password_verify($senha, (string) $usuario['password_hash'])) {
    throw new RuntimeException('E-mail ou senha invalidos.');
  }

  return $usuario;
}

function usuario_criar_token_recuperacao($email)
{
  $usuario = usuario_por_email($email);
  if (!$usuario || (int) $usuario['ativo'] !== 1) {
    return null;
  }

  $token = bin2hex(random_bytes(32));
  $tokenHash = hash('sha256', $token);
  $expiraEm = (new DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:s');

  $mysqli = db_connection();
  $sql = 'UPDATE relatorio_usuarios
    SET reset_token_hash = ?, reset_expires_at = ?
    WHERE id = ?';
  $stmt = $mysqli->prepare($sql);
  $id = (int) $usuario['id'];
  $stmt->bind_param('ssi', $tokenHash, $expiraEm, $id);
  $stmt->execute();

  return $token;
}

function usuario_por_token_recuperacao($token)
{
  $tokenHash = hash('sha256', $token);
  $agora = date('Y-m-d H:i:s');

  $mysqli = db_connection();
  $sql = 'SELECT id, nome, email
    FROM relatorio_usuarios
    WHERE reset_token_hash = ?
      AND reset_expires_at > ?
      AND ativo = 1
    LIMIT 1';
  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param('ss', $tokenHash, $agora);
  $stmt->execute();

  $stmt->bind_result($id, $nome, $email);
  if (!$stmt->fetch()) {
    return null;
  }

  return [
    'id' => $id,
    'nome' => $nome,
    'email' => $email,
  ];
}

function usuario_atualizar_senha_por_token($token, $senha)
{
  $usuario = usuario_por_token_recuperacao($token);
  if (!$usuario) {
    throw new RuntimeException('Link de recuperacao invalido ou expirado.');
  }

  $hash = password_hash($senha, PASSWORD_DEFAULT);
  $mysqli = db_connection();
  $sql = 'UPDATE relatorio_usuarios
    SET password_hash = ?, reset_token_hash = NULL, reset_expires_at = NULL
    WHERE id = ?';
  $stmt = $mysqli->prepare($sql);
  $id = (int) $usuario['id'];
  $stmt->bind_param('si', $hash, $id);
  $stmt->execute();
}

function url_base_app()
{
  $base = app_config('APP_BASE_URL', '');
  if ($base !== '') {
    return rtrim($base, '/');
  }

  $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
  $scheme = $https ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

  return $scheme . '://' . $host;
}

function enviar_email_recuperacao($email, $token)
{
  $link = url_base_app() . '/recuperar.php?token=' . urlencode($token);
  $assunto = 'Recuperacao de senha - Relatorio Feedback Kazza';
  $mensagem = "Use o link abaixo para criar uma nova senha:\n\n{$link}\n\nEste link expira em 1 hora.";
  $from = app_config('MAIL_FROM', 'no-reply@kazzapersianas.com.br');
  $headers = [
    'From: ' . $from,
    'Content-Type: text/plain; charset=UTF-8',
  ];

  return mail($email, $assunto, $mensagem, implode("\r\n", $headers));
}
