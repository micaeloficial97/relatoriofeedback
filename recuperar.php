<?php
require __DIR__.'/back/session.php';
require __DIR__.'/back/auth_mysql.php';

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['token'])) {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
        http_response_code(403);
        exit('CSRF invalido');
    }

    $email = strtolower(trim($_POST['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Informe um e-mail valido.'];
        header('Location: recuperar.php');
        exit;
    }

    try {
        $resetToken = usuario_criar_token_recuperacao($email);

        if ($resetToken !== null && !enviar_email_recuperacao($email, $resetToken)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Nao foi possivel enviar o e-mail de recuperacao. Verifique a configuracao de e-mail do servidor.'];
            header('Location: recuperar.php');
            exit;
        }

        $_SESSION['flash_ok'] = 'Se este e-mail estiver cadastrado, voce recebera um link de recuperacao em instantes.';
        header('Location: login.php');
        exit;
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Nao foi possivel processar a recuperacao de senha.'];
        header('Location: recuperar.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="" href="img/logo-reduzida.png">
  <link rel="stylesheet" href="css/registrar.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Kazza</title>
</head>
<body>
  <div class="login-box">
    <section class="card">
      <div class="login-logo">
        <img src="img/logo.svg" alt="">
      </div>
      <div class="login-box-body">
        <h1 class="login-title"><?= $token ? 'Definir nova senha' : 'Recuperar senha' ?></h1>

        <?php if (!empty($_SESSION['flash_ok'])): ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['flash_ok'], ENT_QUOTES) ?>
          </div>
          <?php unset($_SESSION['flash_ok']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES) ?>
          </div>
          <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; ?>
          <div class="alert alert-<?= htmlspecialchars($f['type']) ?>">
            <?= htmlspecialchars($f['msg']) ?>
          </div>
          <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php if (empty($token)): ?>
          <form action="recuperar.php" method="post" autocomplete="on">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">
            <div class="form-group has-feedback">
              <input type="email" name="email" id="email" class="form-control" placeholder=" " required>
              <label for="email">E-mail</label>
              <span class="bi bi-envelope form-control-feedback"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Enviar link</button>

            <div class="form-group" style="margin-top:10px">
              <a class="btn btn-link" href="login.php">Voltar ao login</a>
            </div>
          </form>
        <?php else: ?>
          <form action="back/update_password.php" method="post" autocomplete="off" id="form-newpass">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES) ?>">

            <div class="form-group has-feedback">
              <input type="password" name="password" id="password" class="form-control" placeholder=" " minlength="6" required>
              <label for="password">Nova senha</label>
              <span class="bi bi-lock form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
              <input type="password" id="confirm" class="form-control" placeholder=" " minlength="6" required>
              <label for="confirm">Confirmar senha</label>
              <span class="bi bi-lock form-control-feedback"></span>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Salvar senha</button>

            <div class="form-group" style="margin-top:10px">
              <a class="btn btn-link" href="login.php">Voltar ao login</a>
            </div>
          </form>

          <script>
            document.getElementById('form-newpass')?.addEventListener('submit', function(e){
              const a = document.getElementById('password').value.trim();
              const b = document.getElementById('confirm').value.trim();
              if (a !== b) { e.preventDefault(); alert('As senhas nao coincidem.'); }
            });
          </script>
        <?php endif; ?>
      </div>
    </section>
  </div>
</body>
</html>
