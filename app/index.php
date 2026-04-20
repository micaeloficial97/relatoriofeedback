<?php
// /app/index.php
require __DIR__ . '/../back/auth.php';
require __DIR__ . '/../back/db.php';

try {
  $rows = feedback_listar(2000);
} catch (Throwable $e) {
  $err = htmlspecialchars($e->getMessage(), ENT_QUOTES);
  echo "<p style='color:#c00'>Erro ao carregar dados: {$err}</p>";
  echo "<p><a href=\"../back/logout.php\">Sair</a></p>";
  exit;
}

function simNao($v) {
  return ($v === true || $v === 1 || $v === '1' || $v === 't' || $v === 'true') ? 'Sim' : 'Nao';
}

function formatarData($valor) {
  if (empty($valor)) {
    return '';
  }

  try {
    return (new DateTime((string) $valor))->format('d-m-Y');
  } catch (Throwable $e) {
    return (string) $valor;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Kazza | Respostas</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Bootstrap 3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <!-- DataTables (Bootstrap 3) -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap.min.css">
  <!-- AdminLTE 2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/css/skins/_all-skins.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="#" class="logo">
      <span class="logo-mini">
        <img src="https://kazza.auge.app/storage/img/Icone_Logo_Kazza_.png" style="filter: grayscale(100%) brightness(10000%);">
      </span>
      <span class="logo-lg">
        <img src="https://kazza.auge.app/storage/img/logoHome.png" alt="Kazza" class="logo-lg-img" style="filter: grayscale(100%) brightness(0%) invert(100%);">
      </span>
    </a>

    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Alternar navegacao</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"><?= htmlspecialchars($_SESSION['nome'] ?? $_SESSION['email'] ?? '', ENT_QUOTES) ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-footer">
                <div class="pull-right">
                  <a href="../back/logout.php" class="btn btn-default btn-flat">Sair</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu">
        <li class="header">MENU</li>
        <li class="active"><a href="#"><i class="fa fa-table"></i> <span>Respostas</span></a></li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Respostas do formulario <small>visualizacao</small></h1>
    </section>

    <section class="content">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Feedback</h3>
        </div>
        <div class="box-body table-responsive">
          <table id="tabela" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Resposta 1</th>
                <th>Resposta 2</th>
                <th>Resposta 3</th>
                <th>Resposta 4</th>
                <th>Evento</th>
                <th>Receber novidades</th>
                <th>Data do evento</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= htmlspecialchars((string)($r['nome'] ?? ''), ENT_QUOTES) ?></td>
                  <td class="text-nowrap"><?= htmlspecialchars((string)($r['email'] ?? ''), ENT_QUOTES) ?></td>
                  <td class="text-nowrap"><?= htmlspecialchars((string)($r['telefone'] ?? ''), ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars((string)($r['resposta1'] ?? ''), ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars((string)($r['resposta2'] ?? ''), ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars((string)($r['resposta3'] ?? ''), ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars((string)($r['resposta4'] ?? ''), ENT_QUOTES) ?></td>
                  <td><?= htmlspecialchars((string)($r['evento'] ?? ''), ENT_QUOTES) ?></td>
                  <td><?= simNao($r['receber_novidades'] ?? 0) ?></td>
                  <td class="text-nowrap"><?= htmlspecialchars(formatarData($r['data'] ?? ''), ENT_QUOTES) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">v1</div>
    <strong>&copy; <?= date('Y') ?> Kazza.</strong> Todos os direitos reservados.
  </footer>

  <div class="control-sidebar-bg"></div>
</div>

<!-- jQuery 2 + Bootstrap 3 + DataTables + AdminLTE 2 -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/js/adminlte.min.js"></script>

<script>
$(function () {
  $('#tabela').DataTable({
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    order: [],
    info: true,
    autoWidth: false,
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
  });
});
</script>
</body>
</html>
