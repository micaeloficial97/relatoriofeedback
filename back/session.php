<?php

session_set_cookie_params([ // cookies de sessão seguros
  'lifetime' => 0,
  'path'     => '/',
  'secure'   => !empty($_SERVER['HTTPS']),
  'httponly' => true,
  'samesite' => 'Lax',
]);
session_start();
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
