<?php

$config = [];
$configPath = __DIR__ . '/config.php';
if (is_file($configPath)) {
  $loaded = require $configPath;
  if (is_array($loaded)) {
    $config = $loaded;
  }
}

function app_config($key, $default = null)
{
  global $config;

  $env = getenv($key);
  if ($env !== false && $env !== '') {
    return $env;
  }

  if (array_key_exists($key, $config) && $config[$key] !== '') {
    return (string) $config[$key];
  }

  return $default;
}

function app_debug()
{
  return filter_var((string) app_config('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
}

function db_connection()
{
  static $mysqli = null;

  if ($mysqli instanceof mysqli) {
    return $mysqli;
  }

  $dbHost = (string) app_config('DB_HOST', '');
  $dbPort = (int) app_config('DB_PORT', '3306');
  $dbName = (string) app_config('DB_NAME', '');
  $dbUser = (string) app_config('DB_USER', '');
  $dbPass = (string) app_config('DB_PASS', '');
  $dbCharset = (string) app_config('DB_CHARSET', 'utf8mb4');

  if ($dbHost === '' || $dbName === '' || $dbUser === '') {
    throw new RuntimeException('Configuracao de banco incompleta.');
  }

  $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
  if ($mysqli->connect_errno) {
    throw new RuntimeException('Erro ao conectar no banco: ' . $mysqli->connect_error);
  }

  if (!$mysqli->set_charset($dbCharset)) {
    throw new RuntimeException('Erro ao configurar charset do banco: ' . $mysqli->error);
  }

  return $mysqli;
}

function db_prepare($mysqli, $sql)
{
  $stmt = $mysqli->prepare($sql);
  if (!$stmt) {
    throw new RuntimeException('Erro ao preparar SQL: ' . $mysqli->error);
  }

  return $stmt;
}

function feedback_listar($limit = 2000)
{
  $limit = max(1, min($limit, 5000));
  $mysqli = db_connection();

  $sql = "SELECT
      nome,
      email,
      telefone,
      resposta1,
      resposta2,
      resposta3,
      resposta4,
      evento,
      receber_novidades,
      data
    FROM feedback_workshop
    ORDER BY id DESC
    LIMIT ?";

  $stmt = db_prepare($mysqli, $sql);
  $stmt->bind_param('i', $limit);
  if (!$stmt->execute()) {
    throw new RuntimeException('Erro ao consultar feedbacks: ' . $stmt->error);
  }

  $stmt->bind_result(
    $nome,
    $email,
    $telefone,
    $resposta1,
    $resposta2,
    $resposta3,
    $resposta4,
    $evento,
    $receberNovidades,
    $data
  );

  $rows = [];
  while ($stmt->fetch()) {
    $rows[] = [
      'nome' => $nome,
      'email' => $email,
      'telefone' => $telefone,
      'resposta1' => $resposta1,
      'resposta2' => $resposta2,
      'resposta3' => $resposta3,
      'resposta4' => $resposta4,
      'evento' => $evento,
      'receber_novidades' => $receberNovidades,
      'data' => $data,
    ];
  }

  return $rows;
}
