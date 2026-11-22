<?php
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/session.php';
require_once 'includes/layout.php';

$cod_sitio=isset($_GET['cod_sitio'])?intval($_GET['cod_sitio']):0;
$ob=new cSitios(); $m=$ob->verSitioCod($cod_sitio); $sitio=mysqli_fetch_array($m);
if(!$sitio) die('Sitio no válido.');
admin_header('Nueva sección · '.$sitio['nom_sitio'],$cod_sitio);
?>
<div class="row justify-content-center"><div class="col-xl-8"><div class="card"><div class="card-body">
<h5>Nueva sección</h5><p class="text-secondary">Crea una instancia nueva. Puedes partir de la estructura de una sección existente; el diseño público solo aparecerá donde el HTML de esa web tenga su bloque correspondiente.</p>
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Nombre</label><input id="nom_seccion" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Slug</label><input id="slug_seccion" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Estructura inicial</label><select id="base_slug" class="form-select"><option value="">Vacía</option><option value="hero-inicio">Hero</option><option value="demo-interactiva">Demo módulos</option><option value="recorrido-encomienda">Recorrido encomienda</option><option value="beneficios-inicio">Beneficios</option><option value="contacto-inicio">Contacto</option></select></div>
<div class="col-md-3"><label class="form-label">Global</label><select id="global_seccion" class="form-select"><option value="0">No</option><option value="1">Sí</option></select></div>
<div class="col-md-3"><label class="form-label">Orden global</label><input id="orden_global" type="number" class="form-control" value="0"></div>
<div class="col-md-4"><label class="form-label">Estado</label><select id="estado_seccion" class="form-select"><option value="1">Activa</option><option value="0">Inactiva</option></select></div>
<div class="col-12"><button class="btn btn-primary" onclick="crearSeccion();"><i class="bi bi-plus-lg me-1"></i> Crear sección</button></div>
</div></div></div></div></div>
<script>
jQuery('#nom_seccion').on('input',function(){if(!jQuery('#slug_seccion').data('manual'))jQuery('#slug_seccion').val(msSlug(this.value));});
jQuery('#slug_seccion').on('input',function(){jQuery(this).data('manual',true);});
function crearSeccion(){var parametros={cod_sitio:'<?php echo $cod_sitio; ?>',nom_seccion:jQuery('#nom_seccion').val(),slug_seccion:jQuery('#slug_seccion').val(),base_slug:jQuery('#base_slug').val(),global_seccion:jQuery('#global_seccion').val(),orden_global:jQuery('#orden_global').val(),estado_seccion:jQuery('#estado_seccion').val(),action:'crearSeccionCms'};jQuery.ajax({data:parametros,url:'ajax.php',type:'post',beforeSend:function(){},success:function(r){if(r==='ok')window.location='secciones.php?cod_sitio=<?php echo $cod_sitio; ?>';else alert(r);}});}
</script>
<?php admin_footer(); ?>
