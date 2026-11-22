<?php
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/session.php';
require_once 'includes/layout.php';

$ob_s=new cSitios();
$matriz_s=$ob_s->verSitios();

admin_header('Sitios web');
?>

<div class="card">
<div class="card-header border-0 d-flex justify-content-between align-items-center">
<div>
<h5 class="mb-1">Sitios administrados</h5>
<small class="text-secondary">Solo URL base. No se guardan rutas físicas.</small>
</div>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSitio" onclick="nuevoSitio();">
Nuevo sitio
</button>
</div>

<div class="card-body">
<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>Sitio</th>
<th>URL</th>
<th>Token</th>
<th>Estado</th>
<th></th>
</tr>
</thead>
<tbody>

<?php while($fila=mysqli_fetch_array($matriz_s)){ ?>

<tr>
<td>
<strong><?php echo htmlspecialchars($fila['nom_sitio']); ?></strong>
<div class="small text-secondary"><?php echo htmlspecialchars($fila['slug_sitio']); ?></div>
</td>
<td>
<a href="<?php echo htmlspecialchars($fila['url_sitio']); ?>" target="_blank">
<?php echo htmlspecialchars($fila['url_sitio']); ?>
</a>
</td>
<td><code><?php echo htmlspecialchars($fila['token_sitio']); ?></code></td>
<td><?php echo $fila['estado_sitio']==1 ? 'Activo' : 'Inactivo'; ?></td>
<td class="text-end">
<a class="btn btn-sm btn-primary" href="paginas.php?cod_sitio=<?php echo $fila['cod_sitio']; ?>">Administrar</a>
<button class="btn btn-sm btn-outline-secondary"
        onclick='editarSitio(<?php echo json_encode($fila,JSON_HEX_APOS|JSON_HEX_QUOT); ?>);'>
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

<div class="modal fade" id="modalSitio">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Sitio</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">

<input type="hidden" id="cod_sitio" value="0">

<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Nombre</label>
<input type="text" id="nom_sitio" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">Slug</label>
<input type="text" id="slug_sitio" class="form-control">
</div>

<div class="col-12">
<label class="form-label">URL base</label>
<input type="text" id="url_sitio" class="form-control" placeholder="http://localhost/metaroad-new/">
</div>

<div class="col-12">
<label class="form-label">Token</label>
<div class="input-group">
<input type="text" id="token_sitio" class="form-control">
<button class="btn btn-outline-secondary" onclick="generarToken();">Generar</button>
</div>
<div class="form-text">
Debe coincidir con el token del archivo admin-upload.php instalado en el sitio.
</div>
</div>

<div class="col-md-4">
<label class="form-label">Estado</label>
<select id="estado_sitio" class="form-select">
<option value="1">Activo</option>
<option value="0">Inactivo</option>
</select>
</div>

</div>
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
<button class="btn btn-primary" onclick="guardarSitio();">Guardar</button>
</div>
</div>
</div>
</div>

<script>
function nuevoSitio()
{
    jQuery('#cod_sitio').val(0);
    jQuery('#nom_sitio,#slug_sitio,#url_sitio,#token_sitio').val('');
    jQuery('#estado_sitio').val(1);
}

function editarSitio(fila)
{
    jQuery('#cod_sitio').val(fila.cod_sitio);
    jQuery('#nom_sitio').val(fila.nom_sitio);
    jQuery('#slug_sitio').val(fila.slug_sitio);
    jQuery('#url_sitio').val(fila.url_sitio);
    jQuery('#token_sitio').val(fila.token_sitio);
    jQuery('#estado_sitio').val(fila.estado_sitio);

    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSitio')).show();
}

function generarToken()
{
    var a=new Uint8Array(24);
    crypto.getRandomValues(a);
    var token=Array.from(a).map(function(b){
        return b.toString(16).padStart(2,'0');
    }).join('');
    jQuery('#token_sitio').val(token);
}

jQuery('#nom_sitio').on('keyup',function(){
    if(jQuery('#cod_sitio').val()==0){
        jQuery('#slug_sitio').val(msSlug(jQuery(this).val()));
    }
});

function guardarSitio()
{
    var parametros={
        "cod_sitio":jQuery('#cod_sitio').val(),
        "nom_sitio":jQuery('#nom_sitio').val(),
        "slug_sitio":jQuery('#slug_sitio').val(),
        "url_sitio":jQuery('#url_sitio').val(),
        "token_sitio":jQuery('#token_sitio').val(),
        "estado_sitio":jQuery('#estado_sitio').val(),
        "action":"guardarSitio"
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
