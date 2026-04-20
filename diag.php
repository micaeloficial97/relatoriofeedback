<?php
require __DIR__ . '/back/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "Relatorio Feedback - Diagnostico\n";
echo "PHP_VERSION: " . PHP_VERSION . "\n";
echo "PHP_SAPI: " . PHP_SAPI . "\n";
echo "mysqli extension: " . (extension_loaded('mysqli') ? 'SIM' : 'NAO') . "\n";
echo "password_hash: " . (function_exists('password_hash') ? 'SIM' : 'NAO') . "\n";
echo "random_bytes: " . (function_exists('random_bytes') ? 'SIM' : 'NAO') . "\n";
echo "config.php: " . (is_file(__DIR__ . '/back/config.php') ? 'SIM' : 'NAO') . "\n";
echo "APP_DEBUG: " . (app_debug() ? 'true' : 'false') . "\n";

try {
  $db = db_connection();
  echo "db_connection: OK\n";

  $result = $db->query("SHOW TABLES LIKE 'relatorio_usuarios'");
  echo "relatorio_usuarios: " . ($result && $result->num_rows > 0 ? 'SIM' : 'NAO') . "\n";

  $result = $db->query("SHOW TABLES LIKE 'feedback_workshop'");
  echo "feedback_workshop: " . ($result && $result->num_rows > 0 ? 'SIM' : 'NAO') . "\n";

  $result = $db->query("SELECT COUNT(*) AS total FROM relatorio_usuarios");
  $row = $result ? $result->fetch_assoc() : ['total' => '?'];
  echo "total usuarios: " . $row['total'] . "\n";
} catch (Exception $e) {
  echo "ERRO: " . $e->getMessage() . "\n";
}

$log = __DIR__ . '/back/app_error.log';
if (is_file($log)) {
  echo "\nUltimas linhas do app_error.log:\n";
  $linhas = file($log, FILE_IGNORE_NEW_LINES) ?: [];
  foreach (array_slice($linhas, -10) as $linha) {
    echo $linha . "\n";
  }
}
