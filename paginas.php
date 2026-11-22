<?php
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/session.php';
require_once 'includes/layout.php';

$cod_sitio=isset($_GET['cod_sitio']) ? intval($_GET['cod_sitio']) : 0;

$ob_s=new cSitios();
$fila_s=mysqli_fetch_array($ob_s->verSitioCod($cod_sitio));

$ob_p=new cPaginas();
$matriz_p=$ob_p->verPaginasSitio($cod_sitio);

admin_header('Páginas · '.$fila_s['nom_sitio'],$cod_sitio);
?>

<div class="card">
<div class="card-header border-0 d-flex justify-content-between align-items-center">
<div>
<h5 class="mb-1">Páginas</h5>
<small class="text-secondary"><?php echo htmlspecialchars($fila_s['url_sitio']); ?></small>
</div>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPagina" onclick="nuevaPagina();">
Nueva página
</button>
</div>

<div class="card-body">
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>Página</th>
<th>Slug</th>
<th>Ruta</th>
<th>Estado</th>
<th></th>
</tr>
</thead>
<tbody>

<?php while($fila=mysqli_fetch_array($matriz_p)){ ?>

<tr>
<td>
<strong><?php echo htmlspecialchars($fila['nom_pagina']); ?></strong>
<div class="small text-secondary"><?php echo htmlspecialchars($fila['titulo_pagina']); ?></div>
</td>
<td><code><?php echo htmlspecialchars($fila['slug_pagina']); ?></code></td>
<td><?php echo htmlspecialchars($fila['ruta_pagina']); ?></td>
<td><?php echo $fila['estado_pagina']==1 ? 'Activa' : 'Inactiva'; ?></td>
<td class="text-end">
<a class="btn btn-sm btn-primary"
   href="pagina-secciones.php?cod_pagina=<?php echo $fila['cod_pagina']; ?>">
Secciones
</a>
<button class="btn btn-sm btn-outline-secondary"
        onclick='editarPagina(<?php echo json_encode($fila,JSON_HEX_APOS|JSON_HEX_QUOT); ?>);'>
Editar
</button>
</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>
</div>
</div>

<div class="modal fade" id="modalPagina">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Página</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" id="cod_pagina" value="0">
<input type="hidden" id="cod_sitio" value="<?php echo $cod_sitio; ?>">

<div class="row g-3">
<div class="col-md-6">
<label class="form-label">Nombre</label>
<input type="text" id="nom_pagina" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">Slug</label>
<input type="text" id="slug_pagina" class="form-control">
</div>

<div class="col-12">
<label class="form-label">Título</label>
<input type="text" id="titulo_pagina" class="form-control">
</div>

<div class="col-12">
<label class="form-label">Descripción</label>
<textarea id="descripcion_pagina" class="form-control" rows="3"></textarea>
</div>

<div class="col-md-8">
<label class="form-label">Ruta</label>
<input type="text" id="ruta_pagina" class="form-control" placeholder="/">
</div>

<div class="col-md-4">
<label class="form-label">Estado</label>
<select id="estado_pagina" class="form-select">
<option value="1">Activa</option>
<option value="0">Inactiva</option>
</select>
</div>
</div>
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
<button class="btn btn-primary" onclick="guardarPagina();">Guardar</button>
</div>
</div>
</div>
</div>

<script>
function nuevaPagina()
{
    jQuery('#cod_pagina').val(0);
    jQuery('#nom_pagina,#slug_pagina,#titulo_pagina,#descripcion_pagina,#ruta_pagina').val('');
    jQuery('#estado_pagina').val(1);
}

function editarPagina(fila)
{
    jQuery('#cod_pagina').val(fila.cod_pagina);
    jQuery('#nom_pagina').val(fila.nom_pagina);
    jQuery('#slug_pagina').val(fila.slug_pagina);
    jQuery('#titulo_pagina').val(fila.titulo_pagina);
    jQuery('#descripcion_pagina').val(fila.descripcion_pagina);
    jQuery('#ruta_pagina').val(fila.ruta_pagina);
    jQuery('#estado_pagina').val(fila.estado_pagina);

    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPagina')).show();
}

jQuery('#nom_pagina').on('keyup',function(){
    if(jQuery('#cod_pagina').val()==0){
        jQuery('#slug_pagina').val(msSlug(jQuery(this).val()));
    }
});

function guardarPagina()
{
    var parametros={
        "cod_pagina":jQuery('#cod_pagina').val(),
        "cod_sitio":jQuery('#cod_sitio').val(),
        "nom_pagina":jQuery('#nom_pagina').val(),
        "slug_pagina":jQuery('#slug_pagina').val(),
        "titulo_pagina":jQuery('#titulo_pagina').val(),
        "descripcion_pagina":jQuery('#descripcion_pagina').val(),
        "ruta_pagina":jQuery('#ruta_pagina').val(),
        "estado_pagina":jQuery('#estado_pagina').val(),
        "action":"guardarPagina"
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
