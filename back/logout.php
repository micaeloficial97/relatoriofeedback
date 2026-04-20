<?php
require __DIR__.'/session.php';
$_SESSION = [];
session_destroy();
header('Location: ../login.php'); exit;
