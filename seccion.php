<?php
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/session.php';
require_once 'includes/layout.php';
require_once 'includes/config_secciones.php';

function msLabel($clave)
{
    $labels=array(
        'textos'=>'Textos','imagenes'=>'Imágenes','botones'=>'Botones','items'=>'Elementos / cards','pasos'=>'Pasos','formulario'=>'Formulario','datos'=>'Datos de contacto','estilos'=>'Diseño y colores','animaciones'=>'Animaciones','configuracion'=>'Configuración',
        'etiqueta'=>'Etiqueta superior','titulo'=>'Título','titulo_destacado'=>'Título destacado','subtitulo'=>'Subtítulo','descripcion'=>'Descripción','descripcion_corta'=>'Descripción corta','texto'=>'Texto','texto_scroll'=>'Texto inferior','texto_boton'=>'Texto del botón','texto_whatsapp'=>'Texto WhatsApp','boton_demo'=>'Botón demo','copyright'=>'Copyright','ubicacion'=>'Ubicación','telefonos'=>'Teléfonos',
        'slug'=>'Identificador','enlace'=>'Enlace','icono'=>'Ícono','imagen'=>'Imagen','fondo'=>'Fondo','logo'=>'Logo','bus'=>'Bus','bus_principal'=>'Bus principal','plataforma'=>'Plataforma','anillos'=>'Anillos','particulas'=>'Partículas','overlay'=>'Overlay','road_glow'=>'Iluminación carretera','dashboard'=>'Dashboard','paquete'=>'Paquete','ondas'=>'Ondas decorativas',
        'orden'=>'Orden','estado'=>'Estado','desplegable'=>'Tiene submenú','requerido'=>'Campo obligatorio','numero'=>'Número','label'=>'Etiqueta','placeholder'=>'Placeholder','direccion'=>'Dirección','telefono_1'=>'Teléfono','whatsapp'=>'WhatsApp','correo'=>'Correo','mensaje'=>'Mensaje','empresa'=>'Empresa','nombre'=>'Nombre',
        'color'=>'Color','background'=>'Fondo','background_color'=>'Color de fondo','border_color'=>'Color borde','glow_color'=>'Color glow','font_size'=>'Tamaño','font_weight'=>'Peso','text_align'=>'Alineación','line_height'=>'Interlineado','letter_spacing'=>'Espaciado','text_transform'=>'Transformación','border_radius'=>'Radio borde','active_color'=>'Color activo','active_background'=>'Fondo activo','active_border'=>'Borde activo','color_activo'=>'Color activo','fondo_activo'=>'Fondo activo','label_color'=>'Color etiquetas','input_background'=>'Fondo campos','input_color'=>'Texto campos','required_color'=>'Color asterisco','button_background'=>'Fondo botón','button_color'=>'Texto botón','icon_color'=>'Color ícono','accent'=>'Color principal','panel_background'=>'Fondo panel','panel_border'=>'Borde panel','completado'=>'Color completado',
        'activo'=>'Activo','tipo'=>'Tipo','duracion'=>'Duración (s)','delay'=>'Retraso (s)','intensidad'=>'Intensidad','seccion'=>'Sección','cards'=>'Cards','bus'=>'Bus','titulo'=>'Título','particulas'=>'Partículas','cambio_modulo'=>'Cambio de módulo','estados'=>'Estados','formulario'=>'Formulario','mapa'=>'Mapa','whatsapp'=>'WhatsApp',
        'asiento'=>'Asiento','ruta'=>'Ruta','fecha_hora'=>'Fecha y hora','boton'=>'Botón','boleto_titulo'=>'Título boleto','boleto_subtitulo'=>'Subtítulo boleto','boleto_ruta'=>'Ruta boleto','boleto_asiento'=>'Asiento boleto','codigo'=>'Código','titulo_panel'=>'Título panel','ingresos'=>'Ingresos','egresos'=>'Egresos','resultado'=>'Resultado','movimientos'=>'Movimientos','concepto'=>'Concepto','monto'=>'Monto','usuarios'=>'Usuarios','rol'=>'Rol','permisos'=>'Permisos','unidad_principal'=>'Unidad principal','estado_principal'=>'Estado principal','flota'=>'Flota','unidad'=>'Unidad','resumen'=>'Resumen','operativas'=>'Operativas','en_ruta'=>'En ruta','total_ventas'=>'Total ventas','variacion_ventas'=>'Variación ventas','pasajes_vendidos'=>'Pasajes vendidos','variacion_pasajes'=>'Variación pasajes','rutas'=>'Rutas','acciones'=>'Acciones','estilo'=>'Diseño del elemento','animacion'=>'Animación del elemento','escena'=>'Datos de la escena','informacion'=>'Información del popup','modulo_inicial'=>'Módulo inicial','mostrar_mapa'=>'Mostrar mapa','mostrar_whatsapp'=>'Mostrar WhatsApp','mapa_query'=>'Búsqueda Google Maps'
    );
    return isset($labels[$clave]) ? $labels[$clave] : ucfirst(str_replace('_',' ',$clave));
}

function msDecode($v){ return html_entity_decode((string)$v,ENT_QUOTES|ENT_HTML5,'UTF-8'); }
function msEsIcono($clave){ return preg_match('/icono/i',$clave); }
function msEsImagen($clave){ return !msEsIcono($clave) && preg_match('/imagen|fondo|logo|bus|plataforma|anillo|particula|overlay|paquete|dashboard|ondas|road_glow/i',$clave); }
function msEsColor($clave){ return preg_match('/color|background|border_color|glow_color|accent/i',$clave); }
function msEsTextoRico($clave){ return preg_match('/titulo|descripcion|texto|copyright/i',$clave) && !preg_match('/color/i',$clave); }
function msEsListaSimple($valor)
{
    if(!is_array($valor) || count($valor)===0){
        return false;
    }

    $esperado=0;

    foreach($valor as $k=>$v){

        if(
            !is_int($k)
            &&
            !ctype_digit((string)$k)
        ){
            return false;
        }

        if(intval($k)!==$esperado){
            return false;
        }

        if(is_array($v)){
            return false;
        }

        $esperado++;
    }

    return true;
}
function msEsListaObjetos($valor)
{
    if(!is_array($valor) || count($valor)===0){
        return false;
    }

    $esperado=0;

    foreach($valor as $k=>$v){

        if(
            (!is_int($k) && !ctype_digit((string)$k))
            ||
            intval($k)!==$esperado
            ||
            !is_array($v)
        ){
            return false;
        }

        $esperado++;
    }

    return true;
}

function msUrlAsset($archivo,$url_sitio,$carpeta)
{
    $archivo=trim((string)$archivo);
    if($archivo==='') return '';
    if(preg_match('#^(https?:)?//#i',$archivo)) return $archivo;
    if(strpos($archivo,'/')!==false) return rtrim($url_sitio,'/').'/'.ltrim($archivo,'/');
    return rtrim($url_sitio,'/').'/'.trim($carpeta,'/').'/'.$archivo;
}

function msInputBase($path,$clave,$valor,$clase='')
{
    $label=msLabel($clave);
    if(in_array($clave,array('estado','activo','desplegable','requerido','mostrar_mapa','mostrar_whatsapp'))){
?>
<div class="mb-3"><label class="form-label"><?php echo htmlspecialchars($label); ?></label>
<select class="form-select ms-campo <?php echo $clase; ?>" data-path="<?php echo htmlspecialchars($path); ?>" data-kind="number">
<option value="1" <?php echo intval($valor)===1?'selected':''; ?>><?php echo in_array($clave,array('estado','activo'))?'Activo':'Sí'; ?></option>
<option value="0" <?php echo intval($valor)===0?'selected':''; ?>><?php echo in_array($clave,array('estado','activo'))?'Inactivo':'No'; ?></option>
</select></div>
<?php return; }

    if($clave==='font_weight'){
?>
<div class="mb-3"><label class="form-label"><?php echo htmlspecialchars($label); ?></label><select class="form-select ms-campo" data-path="<?php echo htmlspecialchars($path); ?>">
<?php foreach(array('300'=>'Light','400'=>'Normal','500'=>'Medium','600'=>'Semi Bold','700'=>'Bold','800'=>'Extra Bold','900'=>'Black') as $v=>$t){ ?><option value="<?php echo $v; ?>" <?php echo (string)$valor===(string)$v?'selected':''; ?>><?php echo $t; ?></option><?php } ?>
</select></div>
<?php return; }

    if($clave==='text_align'){
?>
<div class="mb-3"><label class="form-label"><?php echo htmlspecialchars($label); ?></label><select class="form-select ms-campo" data-path="<?php echo htmlspecialchars($path); ?>">
<?php foreach(array('left'=>'Izquierda','center'=>'Centro','right'=>'Derecha','justify'=>'Justificado') as $v=>$t){ ?><option value="<?php echo $v; ?>" <?php echo $valor===$v?'selected':''; ?>><?php echo $t; ?></option><?php } ?>
</select></div>
<?php return; }

    if($clave==='text_transform'){
?>
<div class="mb-3"><label class="form-label"><?php echo htmlspecialchars($label); ?></label><select class="form-select ms-campo" data-path="<?php echo htmlspecialchars($path); ?>">
<?php foreach(array('none'=>'Normal','uppercase'=>'MAYÚSCULAS','lowercase'=>'minúsculas','capitalize'=>'Capitalizar') as $v=>$t){ ?><option value="<?php echo $v; ?>" <?php echo $valor===$v?'selected':''; ?>><?php echo $t; ?></option><?php } ?>
</select></div>
<?php return; }

    if($clave==='tipo' && strpos($path,'anim')!==false){
?>
<div class="mb-3"><label class="form-label"><?php echo htmlspecialchars($label); ?></label><select class="form-select ms-campo" data-path="<?php echo htmlspecialchars($path); ?>">
<?php foreach(array('fadeIn'=>'Fade','fadeUp'=>'Subir + fade','fadeDown'=>'Bajar + fade','slideLeft'=>'Entrar izquierda','slideRight'=>'Entrar derecha','scaleIn'=>'Escala','staggerUp'=>'Secuencia','float'=>'Flotar','pulse'=>'Pulso','none'=>'Sin animación') as $v=>$t){ ?><option value="<?php echo $v; ?>" <?php echo $valor===$v?'selected':''; ?>><?php echo $t; ?></option><?php } ?>
</select></div>
<?php return; }

    if(msEsColor($clave) && preg_match('/^#[0-9a-fA-F]{6}$/',(string)$valor)){
?>
<div class="mb-3"><label class="form-label"><?php echo htmlspecialchars($label); ?></label><div class="ms-color-control"><input type="color" class="form-control form-control-color ms-color-picker" value="<?php echo htmlspecialchars($valor); ?>" data-target-path="<?php echo htmlspecialchars($path); ?>"><input type="text" class="form-control ms-campo" value="<?php echo htmlspecialchars($valor); ?>" data-path="<?php echo htmlspecialchars($path); ?>"></div></div>
<?php return; }

    $type=in_array($clave,array('orden','numero','duracion','delay','intensidad'))?'number':'text';
    $step=in_array($clave,array('duracion','delay'))?'0.05':'1';
?>
<div class="mb-3"><label class="form-label"><?php echo htmlspecialchars($label); ?></label><input type="<?php echo $type; ?>" step="<?php echo $step; ?>" class="form-control ms-campo <?php echo $clase; ?>" data-path="<?php echo htmlspecialchars($path); ?>" value="<?php echo htmlspecialchars(msDecode($valor)); ?>" <?php echo $type==='number'?'data-kind="number"':''; ?>></div>
<?php
}

function msRich($path,$clave,$valor)
{
?>
<div class="mb-3 ms-rich-wrap"><div class="d-flex justify-content-between align-items-center mb-1"><label class="form-label mb-0"><?php echo htmlspecialchars(msLabel($clave)); ?></label><small class="text-secondary">Editor enriquecido</small></div><textarea class="form-control ms-editor-html" data-path="<?php echo htmlspecialchars($path); ?>" data-height="<?php echo preg_match('/descripcion|texto/i',$clave)?110:76; ?>"><?php echo htmlspecialchars(msDecode($valor)); ?></textarea></div>
<?php
}

function msIconField($path,$clave,$valor,$iconos,$url_sitio,$carpeta)
{
    $url=msUrlAsset($valor,$url_sitio,$carpeta);
?>
<div class="ms-media-field ms-icon-field mb-3" data-media-path="<?php echo htmlspecialchars($path); ?>">
  <label class="form-label d-block"><?php echo htmlspecialchars(msLabel($clave)); ?></label>
  <div class="ms-media-row">
    <div class="ms-media-preview"><?php if($url!=''){ ?><img src="<?php echo htmlspecialchars($url); ?>" alt=""><?php }else{ ?><i class="bi bi-stars"></i><span>Sin ícono</span><?php } ?></div>
    <div class="flex-grow-1">
      <input class="form-control ms-campo ms-media-value mb-2" data-path="<?php echo htmlspecialchars($path); ?>" value="<?php echo htmlspecialchars($valor); ?>" readonly>
      <div class="d-flex flex-wrap gap-2"><button type="button" class="btn btn-sm btn-outline-primary ms-open-icon-library"><i class="bi bi-grid"></i> Elegir ícono</button><button type="button" class="btn btn-sm btn-outline-secondary ms-trigger-upload" data-kind="icon"><i class="bi bi-cloud-arrow-up"></i> Subir SVG/PNG</button></div>
      <input type="file" class="d-none ms-file-input" accept="image/svg+xml,image/png,image/webp,image/jpeg">
      <div class="ms-upload-progress mt-2"><span></span></div><small class="ms-upload-status text-secondary"></small>
    </div>
  </div>
  <div class="ms-icon-library mt-2 d-none">
  <?php foreach($iconos as $nom=>$archivo){ $iu=msUrlAsset($archivo,$url_sitio,$carpeta); ?>
    <button type="button" class="ms-icon-option" data-value="<?php echo htmlspecialchars($archivo); ?>"><img src="<?php echo htmlspecialchars($iu); ?>" alt=""><small><?php echo htmlspecialchars(msLabel($nom)); ?></small></button>
  <?php } ?>
  </div>
</div>
<?php
}

function msImageField($path,$clave,$valor,$url_sitio,$carpeta)
{
    $url=msUrlAsset($valor,$url_sitio,$carpeta);
?>
<div class="ms-media-field mb-3" data-media-path="<?php echo htmlspecialchars($path); ?>">
  <label class="form-label d-block"><?php echo htmlspecialchars(msLabel($clave)); ?></label>
  <div class="ms-media-row">
    <div class="ms-media-preview"><?php if($url!=''){ ?><img src="<?php echo htmlspecialchars($url); ?>" alt=""><?php }else{ ?><i class="bi bi-image"></i><span>Sin imagen</span><?php } ?></div>
    <div class="flex-grow-1">
      <input class="form-control ms-campo ms-media-value mb-2" data-path="<?php echo htmlspecialchars($path); ?>" value="<?php echo htmlspecialchars($valor); ?>" readonly>
      <div class="ms-dropzone ms-trigger-upload" data-kind="image"><i class="bi bi-cloud-arrow-up"></i><strong>Subir / reemplazar</strong><span>Arrastra o selecciona</span></div>
      <input type="file" class="d-none ms-file-input" accept="image/*,image/svg+xml">
      <div class="ms-upload-progress mt-2"><span></span></div><small class="ms-upload-status text-secondary"></small>
    </div>
  </div>
</div>
<?php
}

function msRenderNode($path,$clave,$valor,$context)
{
    $url_sitio=$context['url_sitio']; $carpeta=$context['carpeta']; $iconos=$context['iconos'];

    if(msEsIcono($clave) && !is_array($valor)){ msIconField($path,$clave,$valor,$iconos,$url_sitio,$carpeta); return; }
    if(msEsImagen($clave) && !is_array($valor)){ msImageField($path,$clave,$valor,$url_sitio,$carpeta); return; }

    if(!is_array($valor)){
        if(msEsTextoRico($clave) && !preg_match('/font_|line_|letter_|text_align|text_transform/i',$clave)) msRich($path,$clave,$valor);
        else msInputBase($path,$clave,$valor);
        return;
    }

    if(msEsListaSimple($valor)){
?>
<div class="ms-subblock mb-3" data-array-path="<?php echo htmlspecialchars($path); ?>"><div class="d-flex justify-content-between align-items-center mb-2"><strong><?php echo htmlspecialchars(msLabel($clave)); ?></strong><button class="btn btn-sm btn-outline-primary ms-add-simple" type="button"><i class="bi bi-plus-lg"></i> Agregar</button></div><div class="ms-simple-list">
<?php foreach($valor as $i=>$v){ ?><div class="input-group mb-2 ms-simple-item"><span class="input-group-text"><?php echo $i+1; ?></span><input class="form-control ms-campo" data-path="<?php echo htmlspecialchars($path.'.'.$i); ?>" value="<?php echo htmlspecialchars(msDecode($v)); ?>"><button class="btn btn-outline-danger ms-remove-row" type="button"><i class="bi bi-trash"></i></button></div><?php } ?>
</div></div>
<?php return; }

    if(msEsListaObjetos($valor)){
?>
<div class="ms-subblock mb-3"><div class="d-flex justify-content-between align-items-center mb-2"><strong><?php echo htmlspecialchars(msLabel($clave)); ?></strong><span class="badge text-bg-secondary"><?php echo count($valor); ?></span></div><div class="accordion ms-object-list" id="acc_<?php echo md5($path); ?>">
<?php foreach($valor as $i=>$item){ $title=isset($item['titulo'])?msDecode($item['titulo']):(isset($item['slug'])?$item['slug']:'Elemento '.($i+1)); ?>
<div class="accordion-item ms-item-card"><h2 class="accordion-header"><button class="accordion-button <?php echo $i===0?'':'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#obj_<?php echo md5($path.'_'.$i); ?>"><span class="me-2 badge rounded-pill text-bg-primary"><?php echo $i+1; ?></span><?php echo htmlspecialchars(strip_tags($title)); ?></button></h2><div id="obj_<?php echo md5($path.'_'.$i); ?>" class="accordion-collapse collapse <?php echo $i===0?'show':''; ?>"><div class="accordion-body"><div class="row g-3">
<?php foreach($item as $k=>$v){ ?><div class="col-12 <?php echo is_array($v)?'':'col-xl-6'; ?>"><?php msRenderNode($path.'.'.$i.'.'.$k,$k,$v,$context); ?></div><?php } ?>
</div></div></div></div>
<?php } ?>
</div></div>
<?php return; }

    // associative object/group
?>
<div class="ms-subblock mb-3"><div class="ms-group-title"><strong><?php echo htmlspecialchars(msLabel($clave)); ?></strong><?php if($clave==='estilos'){ ?><small>Los cambios se aplican al preview en vivo.</small><?php }elseif($clave==='animaciones'){ ?><small>Se reproducen al cambiar una opción.</small><?php } ?></div><div class="row g-3">
<?php foreach($valor as $k=>$v){ ?><div class="col-12 <?php echo is_array($v)?(in_array($clave,array('estilos','animaciones'))?'col-xl-6':''):'col-xl-6'; ?>"><?php msRenderNode($path.'.'.$k,$k,$v,$context); ?></div><?php } ?>
</div></div>
<?php
}

$cod_seccion=isset($_GET['cod_seccion'])?intval($_GET['cod_seccion']):0;
if($cod_seccion<=0) die('Sección no válida.');

$ob=new cSecciones();
$m=$ob->verSeccionCod($cod_seccion);
$fila=mysqli_fetch_array($m);
if(!$fila) die('Sección no encontrada.');

$obs=new cSitios();
$ms=$obs->verSitioCod($fila['cod_sitio']);
$sitio=mysqli_fetch_array($ms);
if(!$sitio) die('Sitio no encontrado.');

$db=json_decode($fila['contenido_seccion'],true); if(!is_array($db)) $db=array();
$config=msConfigSeccionSitio($sitio['slug_sitio'],$fila['slug_seccion']);
$contenido=msCombinarContenido($config['contenido'],$db); // solo guía visual, NO guarda automático
$scope=$config['scope'];
$context=array('url_sitio'=>$sitio['url_sitio'],'carpeta'=>$fila['carpeta_uploads'],'iconos'=>$config['iconos']);

admin_header('Editar sección · '.$fila['nom_seccion'],$fila['cod_sitio']);
?>

<div class="alert alert-info ms-guide"><i class="bi bi-eye me-2"></i><strong>Guía visual activa.</strong> Si MySQL tiene campos vacíos, se muestran los datos del diseño aprobado. Solo se guardan al pulsar <strong>Guardar cambios</strong>.</div>

<style id="ms-preview-critical-css">
.ms-editor-layout{
    display:flex!important;
    align-items:flex-start!important;
    gap:18px!important;
    width:100%!important;
    max-width:none!important;
}
.ms-editor-column{
    flex:1 1 auto!important;
    min-width:0!important;
}
/* #msPreviewColumn{
    display:block!important;
    visibility:visible!important;
    opacity:1!important;
    flex:0 0 440px!important;
    width:440px!important;
    min-width:440px!important;
    position:sticky!important;
    top:72px!important;
    align-self:flex-start!important;
    height:calc(100vh - 88px)!important;
    z-index:100!important;
} */

.app-wrapper, .app-main, .app-content, .container-fluid {
  overflow: visible !important; 
}    

#msPreviewColumn{
    display:block!important;
    visibility:visible!important;
    opacity:1!important;
    flex:0 0 440px!important;
    width:440px!important;
    min-width:440px!important;
    position:sticky!important;
    top:72px!important;
    align-self:flex-start!important;
    height:auto!important; /* IMPORTANTE */
    max-height:calc(100vh - 88px)!important; /* IMPORTANTE */
    z-index:100!important;
}

#msPreviewColumn .ms-preview-shell{
    display:flex!important;
    flex-direction:column!important;
    visibility:visible!important;
    opacity:1!important;
    width:100%!important;
    height:100%!important;
    min-height:520px!important;
    max-height:calc(100vh - 88px)!important;
    background:#20252b!important;
    border:1px solid #30363d!important;
    border-radius:7px!important;
    overflow:hidden!important;
    box-shadow:0 10px 28px rgba(0,0,0,.18)!important;
}
#msPreviewColumn .ms-preview-toolbar{
    display:flex!important;
    flex:0 0 auto!important;
}
#msPreviewColumn .ms-preview-direct-wrap{
    display:block!important;
    flex:1 1 auto!important;
    min-height:0!important;
    background:#071019!important;
    overflow:auto!important;
}
#cmsPreviewHost{
    display:block!important;
    width:100%!important;
    min-height:360px!important;
    background:#071019!important;
    color:#fff!important;
}
.ms-preview-loading-state{
    min-height:360px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    gap:10px;
    color:#aeb8c2;
    text-align:center;
    padding:24px;
}
@media(max-width:1180px){
    .ms-editor-layout{
        display:block!important;
    }
    #msPreviewColumn{
        position:relative!important;
        top:auto!important;
        width:100%!important;
        min-width:0!important;
        height:680px!important;
        margin-top:18px!important;
    }
    #msPreviewColumn .ms-preview-shell{
        height:680px!important;
        max-height:680px!important;
    }
}
</style>

<div class="ms-editor-layout">
  <section class="ms-editor-column">
    <div class="card ms-main-card"><div class="card-body">
      <div class="d-flex justify-content-between gap-3 align-items-start flex-wrap mb-3"><div><h5 class="mb-1">Contenido de <?php echo htmlspecialchars($fila['nom_seccion']); ?></h5><p class="text-secondary mb-0">Edita textos, imágenes, iconos, colores, estilos y animaciones sin tocar JSON.</p></div><button type="button" class="btn btn-primary ms-save-section"><i class="bi bi-floppy me-1"></i> Guardar cambios</button></div>

      <input type="hidden" id="cod_seccion" value="<?php echo intval($cod_seccion); ?>">
      <input type="hidden" id="cod_sitio" value="<?php echo intval($fila['cod_sitio']); ?>">
      <input type="hidden" id="scope_seccion" value="<?php echo htmlspecialchars($scope); ?>">
      <input type="hidden" id="url_sitio" value="<?php echo htmlspecialchars($sitio['url_sitio']); ?>">
      <input type="hidden" id="token_sitio" value="<?php echo htmlspecialchars($sitio['token_sitio']); ?>">

      <div class="ms-meta-box mb-3">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Nombre de la sección</label><input id="nom_seccion" class="form-control" value="<?php echo htmlspecialchars($fila['nom_seccion']); ?>"></div>
          <div class="col-md-6"><label class="form-label">Slug</label><input id="slug_seccion" class="form-control" value="<?php echo htmlspecialchars($fila['slug_seccion']); ?>" readonly></div>
          <div class="col-12"><label class="form-label">Carpeta de uploads</label><input id="carpeta_uploads" class="form-control" value="<?php echo htmlspecialchars($fila['carpeta_uploads']); ?>"><small class="text-secondary">La carpeta se crea automáticamente en el sitio si no existe.</small></div>
        </div>
      </div>

      <?php foreach($contenido as $grupo=>$valor){

        if(is_array($valor) && count($valor)===0){
            continue;
        }

    ?>
      <div class="ms-content-section" data-group="<?php echo htmlspecialchars($grupo); ?>">
        <div class="ms-section-heading"><div><h5><?php echo htmlspecialchars(msLabel($grupo)); ?></h5><small><?php echo $grupo==='estilos'?'Colores, tipografía, alineación y apariencia CSS.':($grupo==='animaciones'?'Entrada, duración, retraso e intensidad.':'Contenido editable de la sección.'); ?></small></div><i class="bi <?php echo $grupo==='estilos'?'bi-palette':($grupo==='animaciones'?'bi-stars':'bi-sliders'); ?>"></i></div>
        <div class="ms-section-body"><div class="row g-3">
        <?php if(is_array($valor) && !msEsListaObjetos($valor) && !msEsListaSimple($valor)){ foreach($valor as $clave=>$v){ ?><div class="col-12 <?php echo is_array($v)?'':'col-xl-6'; ?>"><?php msRenderNode($grupo.'.'.$clave,$clave,$v,$context); ?></div><?php } }else{ ?><div class="col-12"><?php msRenderNode($grupo,$grupo,$valor,$context); ?></div><?php } ?>
        </div></div>
      </div>
      <?php } ?>

      <div class="ms-save-bottom"><button type="button" class="btn btn-primary ms-save-section"><i class="bi bi-floppy me-1"></i> Guardar cambios</button><span class="text-secondary ms-save-state">Cambios sin guardar</span></div>
    </div></div>
  </section>

  <div class="ms-preview-column" id="msPreviewColumn">
    <div class="ms-preview-shell">
      <div class="ms-preview-toolbar">
        <div><strong>Vista previa de la sección</strong><small>HTML + CSS reales de METAROAD</small></div>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-light ms-refresh-preview" type="button" title="Recargar"><i class="bi bi-arrow-clockwise"></i></button>
          <button class="btn btn-sm btn-primary ms-fullscreen-preview" type="button"><i class="bi bi-arrows-fullscreen me-1"></i>Pantalla completa</button>
        </div>
      </div>
      <div class="ms-preview-direct-wrap">
        <div id="cmsPreviewHost" class="ms-preview-host">
          <div class="ms-preview-loading-state" id="cmsPreviewInitialState">
            <div class="spinner-border text-info" role="status" aria-hidden="true"></div>
            <strong>Cargando vista previa…</strong>
            <small>Se mostrará únicamente esta sección.</small>
          </div>
        </div>
      </div>
      <div class="ms-preview-foot"><i class="bi bi-broadcast me-1"></i><span id="previewStatus">Cargando sección…</span></div>
    </div>
  </div>
</div>

<div class="modal fade" id="previewFullscreen" tabindex="-1">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content bg-dark">
      <div class="modal-header border-secondary text-white"><h5 class="modal-title">Vista previa · <?php echo htmlspecialchars($fila['nom_seccion']); ?></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body p-0 ms-preview-full-body"><div id="cmsPreviewFullscreenHost" class="ms-preview-host ms-preview-host-full"></div></div>
    </div>
  </div>
</div>

<script>
window.MS_SECTION_BOOT = <?php echo json_encode(array(
  'cod_seccion'=>intval($cod_seccion),
  'scope'=>$scope,
  'slug_seccion'=>$fila['slug_seccion'],
  'url_sitio'=>$sitio['url_sitio'],
  'token_sitio'=>$sitio['token_sitio'],
  'carpeta_uploads'=>$fila['carpeta_uploads'],
  'contenido'=>$contenido
),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
</script>
<script src="assets/seccion-editor.js?v=20261120-final2"></script>

<?php admin_footer(); ?>
