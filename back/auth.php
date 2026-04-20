<?php
require __DIR__.'/session.php';
if (empty($_SESSION['uid'])) {
  header('Location: ../login.php'); exit;
}
