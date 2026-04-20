<?php require __DIR__.'/back/session.php'; ?>

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
                <h1 class="login-title">Criar uma conta</h1>
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
                <form action="back/register.php" method="post">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES) ?>">
                    <div class="form-group has-feedback">
                        <input type="text" id="name" class="form-control" placeholder=" " name="name" required>
                        <label for="name">Nome</label>
                        <span class="bi bi-person form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="email" id="email" class="form-control" placeholder=" " name="email" required>
                        <label for="email">E-mail</label>
                        <span class="bi bi-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" id="password" class="form-control" placeholder=" " name="password" required>
                        <label for="password">Senha</label>
                        <span class="bi bi-lock form-control-feedback"></span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        Criar conta
                    </button>
                </form>
            </div>
        </section>
    </div>
</body>

</html>