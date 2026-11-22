<?php
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/session.php';
require_once 'includes/layout.php';

$ob_s=new cSitios();
$matriz_s=$ob_s->verSitios();

admin_header('Dashboard');
?>

<div class="row g-4">

<?php while($fila=mysqli_fetch_array($matriz_s)){ ?>

<div class="col-md-6 col-xl-4">
<div class="card h-100">
<div class="card-body">

<div class="d-flex justify-content-between">
<div>
<div class="small text-secondary"><?php echo htmlspecialchars($fila['slug_sitio']); ?></div>
<h4 class="mt-1"><?php echo htmlspecialchars($fila['nom_sitio']); ?></h4>
</div>
<i class="bi bi-globe2 fs-2 text-primary"></i>
</div>

<a href="<?php echo htmlspecialchars($fila['url_sitio']); ?>"
   target="_blank"
   class="small text-break">
<?php echo htmlspecialchars($fila['url_sitio']); ?>
</a>

<div class="d-grid gap-2 mt-4">
<a href="paginas.php?cod_sitio=<?php echo $fila['cod_sitio']; ?>" class="btn btn-primary">
Administrar
</a>
<a href="secciones.php?cod_sitio=<?php echo $fila['cod_sitio']; ?>" class="btn btn-outline-secondary">
Secciones
</a>
</div>

</div>
</div>
</div>

<?php } ?>

</div>

<?php admin_footer(); ?>
