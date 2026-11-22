<?php
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/session.php';
require_once 'includes/layout.php';

$cod_pagina=isset($_GET['cod_pagina']) ? intval($_GET['cod_pagina']) : 0;

$ob_p=new cPaginas();
$fila_p=mysqli_fetch_array($ob_p->verPaginaCod($cod_pagina));

$cod_sitio=$fila_p['cod_sitio'];

$ob_sec=new cSecciones();
$matriz_asignadas=$ob_sec->verSeccionesPagina($cod_pagina);
$matriz_todas=$ob_sec->verSeccionesSitio($cod_sitio);

admin_header('Secciones · '.$fila_p['nom_pagina'],$cod_sitio);
?>

<div class="row g-4">

<div class="col-lg-8">
<div class="card">
<div class="card-header border-0">
<h5 class="mb-1">Secciones asignadas</h5>
<small class="text-secondary">La misma sección puede estar en varias páginas.</small>
</div>

<div class="card-body">
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>Orden</th>
<th>Sección</th>
<th>Carpeta</th>
<th></th>
</tr>
</thead>
<tbody>

<?php while($fila=mysqli_fetch_array($matriz_asignadas)){ ?>

<tr>
<td><?php echo intval($fila['orden_seccion']); ?></td>
<td>
<strong><?php echo htmlspecialchars($fila['nom_seccion']); ?></strong>
<div class="small text-secondary"><?php echo htmlspecialchars($fila['slug_seccion']); ?></div>
</td>
<td><code><?php echo htmlspecialchars($fila['carpeta_uploads']); ?></code></td>
<td class="text-end">
<a class="btn btn-sm btn-primary" href="seccion.php?cod_seccion=<?php echo $fila['cod_seccion']; ?>">Editar</a>
<button class="btn btn-sm btn-outline-danger" onclick="quitarSeccion(<?php echo $fila['cod_seccion']; ?>);">Quitar</button>
</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>
</div>
</div>

<div class="col-lg-4">
<div class="card">
<div class="card-header border-0">
<h5 class="mb-0">Agregar sección existente</h5>
</div>
<div class="card-body">

<label class="form-label">Sección</label>
<select id="cod_seccion" class="form-select mb-3">
<option value="">Seleccionar</option>

<?php while($fila=mysqli_fetch_array($matriz_todas)){ ?>
<option value="<?php echo $fila['cod_seccion']; ?>">
<?php echo htmlspecialchars($fila['nom_seccion']); ?>
</option>
<?php } ?>

</select>

<label class="form-label">Orden</label>
<input type="number" id="orden_seccion" class="form-control mb-3" value="1">

<button class="btn btn-primary w-100" onclick="asignarSeccion();">
Agregar / actualizar
</button>

<hr>

<a href="nueva-seccion.php?cod_sitio=<?php echo $cod_sitio; ?>" class="btn btn-outline-secondary w-100">
Crear nueva sección
</a>

</div>
</div>
</div>

</div>

<script>
function asignarSeccion()
{
    var parametros={
        "cod_pagina":"<?php echo $cod_pagina; ?>",
        "cod_seccion":jQuery('#cod_seccion').val(),
        "orden_seccion":jQuery('#orden_seccion').val(),
        "action":"asignarSeccionPagina"
    };

    jQuery.ajax({
        data:parametros,
        url:'ajax.php',
        type:'post',
        success:function(response){
            if(response=='ok'){
                location.reload();
            }else{
                alert(response);
            }
        }
    });
}

function quitarSeccion(cod_seccion)
{
    if(!confirm('¿Quitar esta sección de la página?')){
        return;
    }

    var parametros={
        "cod_pagina":"<?php echo $cod_pagina; ?>",
        "cod_seccion":cod_seccion,
        "action":"quitarSeccionPagina"
    };

    jQuery.ajax({
        data:parametros,
        url:'ajax.php',
        type:'post',
        success:function(response){
            if(response=='ok'){
                location.reload();
            }else{
                alert(response);
            }
        }
    });
}
</script>

<?php admin_footer(); ?>
