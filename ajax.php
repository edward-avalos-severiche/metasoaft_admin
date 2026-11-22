<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/config_secciones.php';

function respuestaJson($data){ header('Content-Type: application/json; charset=utf-8'); echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }

function textoHtmlEntidades($html)
{
    $html=(string)$html;
    $html=preg_replace('#<script\b[^>]*>.*?</script>#is','',$html);
    $html=preg_replace('/\son[a-z]+\s*=\s*(["\']).*?\1/iu','',$html);
    $html=preg_replace('/javascript\s*:/iu','',$html);
    $html=strip_tags($html,'<p><br><strong><b><em><i><u><s><span><div><ul><ol><li><a><blockquote><h1><h2><h3><h4><h5><h6><sup><sub>');
    $partes=preg_split('/(<[^>]+>)/u',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($partes as $i=>$parte){
        if($parte==='' || substr($parte,0,1)==='<') continue;
        $parte=html_entity_decode($parte,ENT_QUOTES|ENT_HTML5,'UTF-8');
        $partes[$i]=htmlentities($parte,ENT_QUOTES|ENT_HTML5,'UTF-8',false);
    }
    return implode('',$partes);
}

function normalizarContenidoCms($valor,$clave='',$padre='')
{
    if(is_array($valor)){
        $salida=array();
        foreach($valor as $k=>$v) $salida[$k]=normalizarContenidoCms($v,(string)$k,(string)$clave);
        return $salida;
    }
    $es_texto=($padre==='textos' || preg_match('/titulo|subtitulo|descripcion|texto|etiqueta|copyright|label|placeholder|direccion|mensaje|concepto|rol|estado|ruta|ubicacion|telefonos/i',$clave));
    if($es_texto && is_string($valor)) return textoHtmlEntidades($valor);
    return $valor;
}

if(isset($_POST['action']) && $_POST['action']==='login'){
    $user=isset($_POST['user_usuario'])?trim($_POST['user_usuario']):'';
    $pass=isset($_POST['password_usuario'])?md5($_POST['password_usuario']):'';
    $ob=new cUsuarios(); $m=$ob->login($user,$pass); $f=mysqli_fetch_array($m);
    if($f){ $_SESSION['cod_usuario']=$f['cod_usuario']; $_SESSION['nom_usuario']=$f['nom_usuario']; $_SESSION['tipo_usuario']=$f['tipo_usuario']; echo 'ok'; } else echo 'bad'; exit;
}


if(isset($_POST['action']) && $_POST['action']==='crearPrimerAdmin'){
    $ob=new cUsuarios();
    $m=$ob->contarUsuarios();
    $f=mysqli_fetch_array($m);
    if(intval($f['total'])>0){ echo 'ya_existe'; exit; }
    $r=$ob->crearUsuario(
        isset($_POST['nom_usuario'])?$_POST['nom_usuario']:'Administrador',
        isset($_POST['ape_usuario'])?$_POST['ape_usuario']:'',
        isset($_POST['email_usuario'])?$_POST['email_usuario']:'admin@metasoft.local',
        isset($_POST['user_usuario'])?$_POST['user_usuario']:'admin',
        isset($_POST['password_usuario'])?$_POST['password_usuario']:''
    );
    echo $r?'ok':'bad';
    exit;
}

if(!isset($_SESSION['cod_usuario'])){ http_response_code(403); respuestaJson(array('ok'=>false,'mensaje'=>'Sesión vencida.')); }

if(isset($_POST['action']) && $_POST['action']==='guardarSeccionCms'){
    $cod=intval($_POST['cod_seccion']);
    $nom=trim($_POST['nom_seccion']);
    $carpeta=trim($_POST['carpeta_uploads']);
    $contenido=json_decode(isset($_POST['contenido'])?$_POST['contenido']:'{}',true);
    if(!is_array($contenido)) respuestaJson(array('ok'=>false,'mensaje'=>'Contenido no válido.'));
    $contenido=normalizarContenidoCms($contenido);
    $ob=new cSecciones(); $m=$ob->verSeccionCod($cod); $f=mysqli_fetch_array($m); if(!$f) respuestaJson(array('ok'=>false,'mensaje'=>'Sección no encontrada.'));
    $json=json_encode($contenido,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $r=$ob->guardarSeccion($cod,$f['cod_sitio'],$nom,$f['slug_seccion'],$carpeta,$json,$f['global_seccion'],$f['orden_global'],$f['estado_seccion']);
    respuestaJson(array('ok'=>$r?true:false,'mensaje'=>$r?'Sección guardada.':'No se pudo guardar.','contenido'=>$contenido));
}


if(isset($_POST['action']) && $_POST['action']==='crearSeccionCms'){
    $cod_sitio=intval($_POST['cod_sitio']);
    $nom=trim($_POST['nom_seccion']);
    $slug=trim($_POST['slug_seccion']);
    $base_slug=trim($_POST['base_slug']);
    $global=intval($_POST['global_seccion']);
    $orden=intval($_POST['orden_global']);
    $estado=intval($_POST['estado_seccion']);
    if($nom==='' || $slug===''){ echo 'faltan_datos'; exit; }
    $obs=new cSitios(); $ms=$obs->verSitioCod($cod_sitio); $sitio=mysqli_fetch_array($ms); if(!$sitio){ echo 'sitio_no_valido'; exit; }
    $cfg=msConfigSeccionSitio($sitio['slug_sitio'],$base_slug);
    $contenido=is_array($cfg['contenido'])?$cfg['contenido']:array();
    $carpeta='uploads/secciones/'.$slug.'/';
    $json=json_encode(normalizarContenidoCms($contenido),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $ob=new cSecciones();
    $r=$ob->guardarSeccion(0,$cod_sitio,$nom,$slug,$carpeta,$json,$global,$orden,$estado);
    echo $r?'ok':'bad';
    exit;
}

// Acciones existentes simples para otras pantallas.
if(isset($_POST['action']) && $_POST['action']==='guardarSitio'){
    $ob=new cSitios(); $r=$ob->guardarSitio(intval($_POST['cod_sitio']),$_POST['nom_sitio'],$_POST['slug_sitio'],$_POST['url_sitio'],$_POST['token_sitio'],intval($_POST['estado_sitio'])); echo $r?'ok':'bad'; exit;
}
if(isset($_POST['action']) && $_POST['action']==='guardarPagina'){
    $ob=new cPaginas(); $r=$ob->guardarPagina(intval($_POST['cod_pagina']),intval($_POST['cod_sitio']),$_POST['nom_pagina'],$_POST['slug_pagina'],$_POST['titulo_pagina'],$_POST['descripcion_pagina'],$_POST['ruta_pagina'],intval($_POST['estado_pagina'])); echo $r?'ok':'bad'; exit;
}
if(isset($_POST['action']) && $_POST['action']==='asignarSeccionPagina'){
    $ob=new cSecciones(); $r=$ob->asignarSeccionPagina(intval($_POST['cod_pagina']),intval($_POST['cod_seccion']),intval($_POST['orden_seccion'])); echo $r?'ok':'bad'; exit;
}
if(isset($_POST['action']) && $_POST['action']==='quitarSeccionPagina'){
    $ob=new cSecciones(); $r=$ob->quitarSeccionPagina(intval($_POST['cod_pagina']),intval($_POST['cod_seccion'])); echo $r?'ok':'bad'; exit;
}

respuestaJson(array('ok'=>false,'mensaje'=>'Acción no reconocida.'));
?>
