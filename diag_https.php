<?php
header('Content-Type: text/plain; charset=utf-8');

echo "PHP: ".PHP_VERSION."\n";
$need = ['curl','openssl','pdo_pgsql','mbstring','json'];
foreach ($need as $ext) {
  echo sprintf("%-10s: %s\n", $ext, extension_loaded($ext) ? 'ok' : 'FALTA');
}

// testa HTTPS outbound sem segredo nenhum
$ch = curl_init('https://example.com/');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>10]);
$body = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "cURL https test: HTTP $code ".($err ? "($err)" : "ok")."\n";
