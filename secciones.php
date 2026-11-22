<?php
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/session.php';
require_once 'includes/layout.php';

$cod_sitio=isset($_GET['cod_sitio']) ? intval($_GET['cod_sitio']) : 0;

$ob_s=new cSitios();
$fila_s=mysqli_fetch_array($ob_s->verSitioCod($cod_sitio));

$ob_sec=new cSecciones();
$matriz_sec=$ob_sec->verSeccionesSitio($cod_sitio);

admin_header('Secciones · '.$fila_s['nom_sitio'],$cod_sitio);
?>

<div class="card">
<div class="card-header border-0 d-flex justify-content-between align-items-center">
<div>
<h5 class="mb-1">Secciones</h5>
<small class="text-secondary">Una fila por sección con contenido_seccion completo en JSON.</small>
</div>

<a href="nueva-seccion.php?cod_sitio=<?php echo $cod_sitio; ?>" class="btn btn-primary">
Nueva sección
</a>
</div>

<div class="card-body">
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>Sección</th>
<th>Carpeta uploads</th>
<th>Uso</th>
<th>Estado</th>
<th></th>
</tr>
</thead>

<tbody>
<?php while($fila=mysqli_fetch_array($matriz_sec)){ ?>

<tr>
<td>
<strong><?php echo htmlspecialchars($fila['nom_seccion']); ?></strong>
<div class="small text-secondary"><?php echo htmlspecialchars($fila['slug_seccion']); ?></div>
</td>
<td><code><?php echo htmlspecialchars($fila['carpeta_uploads']); ?></code></td>
<td><?php echo $fila['global_seccion']==1 ? 'Global' : 'Reutilizable'; ?></td>
<td><?php echo $fila['estado_seccion']==1 ? 'Activa' : 'Inactiva'; ?></td>
<td class="text-end">
<a class="btn btn-sm btn-primary" href="seccion.php?cod_seccion=<?php echo $fila['cod_seccion']; ?>">
Editar
</a>
</td>
</tr>

<?php } ?>
</tbody>
</table>
</div>
</div>
</div>

<?php admin_footer(); ?>
