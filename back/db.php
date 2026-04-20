<?php
declare(strict_types=1);

$config = [];
$configPath = __DIR__ . '/config.php';
if (is_file($configPath)) {
  $loaded = require $configPath;
  if (is_array($loaded)) {
    $config = $loaded;
  }
}

function app_config(string $key, ?string $default = null): ?string
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

function db_connection(): mysqli
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

  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
  $mysqli->set_charset($dbCharset);

  return $mysqli;
}

function feedback_listar(int $limit = 2000): array
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

  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param('i', $limit);
  $stmt->execute();

  $result = $stmt->get_result();
  return $result->fetch_all(MYSQLI_ASSOC);
}
