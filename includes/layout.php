<?php
function admin_header($titulo='Admin MetaSoft',$cod_sitio=0)
{
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($titulo); ?> | MetaSoft</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.css">
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.js"></script>
<script src="assets/admin.js?v=20261120-final2"></script>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#"><i class="bi bi-list"></i></a></li></ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><span class="nav-link"><i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars(isset($_SESSION['nom_usuario'])?$_SESSION['nom_usuario']:'Administrador'); ?></span></li>
      <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i></a></li>
    </ul>
  </div>
</nav>
<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
  <div class="sidebar-brand"><a href="dashboard.php" class="brand-link"><span class="brand-text fw-bold">METASOFT</span></a></div>
  <div class="sidebar-wrapper"><nav class="mt-2"><ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview">
    <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon bi bi-speedometer2"></i><p>Dashboard</p></a></li>
    <li class="nav-item"><a href="sitios.php" class="nav-link"><i class="nav-icon bi bi-globe2"></i><p>Sitios web</p></a></li>
    <?php if($cod_sitio>0){ ?>
      <li class="nav-header">SITIO ACTUAL</li>
      <li class="nav-item"><a href="paginas.php?cod_sitio=<?php echo intval($cod_sitio); ?>" class="nav-link"><i class="nav-icon bi bi-file-earmark-text"></i><p>Páginas</p></a></li>
      <li class="nav-item"><a href="secciones.php?cod_sitio=<?php echo intval($cod_sitio); ?>" class="nav-link"><i class="nav-icon bi bi-layout-text-window"></i><p>Secciones</p></a></li>
    <?php } ?>
  </ul></nav></div>
</aside>
<main class="app-main">
  <div class="app-content-header"><div class="container-fluid"><h3 class="mb-0"><?php echo htmlspecialchars($titulo); ?></h3></div></div>
  <div class="app-content"><div class="container-fluid">
<?php
}

function admin_footer()
{
?>
  </div></div>
</main>
<footer class="app-footer"><strong>MetaSoft</strong><span class="float-end d-none d-sm-inline">CRM Multiweb</span></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>
</body>
</html>
<?php
}
?>
