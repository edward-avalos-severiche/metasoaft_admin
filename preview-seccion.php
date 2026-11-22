<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';
require_once 'includes/config_secciones.php';

header('Content-Type: application/json; charset=utf-8');

function outPreview($data){
    echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    exit;
}

if(!isset($_SESSION['cod_usuario'])){
    http_response_code(403);
    outPreview(array('ok'=>false,'message'=>'Sesión vencida.'));
}

function fetchCms($url)
{
    if(function_exists('curl_init')){
        $ch=curl_init($url);
        curl_setopt_array($ch,array(
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_FOLLOWLOCATION=>true,
            CURLOPT_CONNECTTIMEOUT=>4,
            CURLOPT_TIMEOUT=>12,
            CURLOPT_USERAGENT=>'MetaSoftCMSPreview/2.0',
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false
        ));
        $body=curl_exec($ch);
        $code=intval(curl_getinfo($ch,CURLINFO_HTTP_CODE));
        curl_close($ch);
        if($body!==false && $code>=200 && $code<400) return $body;
    }

    $ctx=stream_context_create(array(
        'http'=>array('timeout'=>12,'follow_location'=>1),
        'ssl'=>array('verify_peer'=>false,'verify_peer_name'=>false)
    ));
    return @file_get_contents($url,false,$ctx);
}

function extractCmsMarker($html,$name)
{
    $start='<!--CMS_SECTION:'.$name.':START-->';
    $end='<!--CMS_SECTION:'.$name.':END-->';
    $p1=strpos($html,$start);
    if($p1===false) return false;
    $p1+=strlen($start);
    $p2=strpos($html,$end,$p1);
    if($p2===false) return false;
    return substr($html,$p1,$p2-$p1);
}

function removeCmsMarker($html,$name)
{
    $start='<!--CMS_SECTION:'.$name.':START-->';
    $end='<!--CMS_SECTION:'.$name.':END-->';
    $p1=strpos($html,$start);
    if($p1===false) return $html;
    $p2=strpos($html,$end,$p1);
    if($p2===false) return $html;
    $p2+=strlen($end);
    return substr($html,0,$p1).substr($html,$p2);
}

function absCms($base,$rel)
{
    $rel=trim((string)$rel);
    if($rel==='' || preg_match('#^(?:https?:|data:|//)#i',$rel)) return $rel;
    return rtrim($base,'/').'/'.ltrim($rel,'/');
}

function rewriteFragmentCms($html,$base)
{
    return preg_replace_callback('/\\b(src|href|poster)=("|\\\')([^"\\\']+)\\2/i',function($m) use($base){
        $v=$m[3];
        if($v==='' || $v[0]==='#' || preg_match('#^(?:https?:|data:|mailto:|tel:|javascript:|//)#i',$v)) return $m[0];
        return $m[1].'='.$m[2].absCms($base,$v).$m[2];
    },$html);
}

function rewriteCssCms($css,$cssUrl)
{
    $base=preg_replace('#/[^/]*$#','/',$cssUrl);
    return preg_replace_callback('#url\\(\\s*(["\\\']?)([^)"\\\']+)\\1\\s*\\)#i',function($m) use($base){
        $u=trim($m[2]);
        if($u==='' || preg_match('#^(?:data:|https?:|//)#i',$u)) return $m[0];
        return 'url("'.rtrim($base,'/').'/'.ltrim($u,'/').' ")';
    },$css);
}

$cod=intval(isset($_GET['cod_seccion'])?$_GET['cod_seccion']:0);
if($cod<=0) outPreview(array('ok'=>false,'message'=>'Sección no válida.'));

$ob=new cSecciones();
$f=mysqli_fetch_array($ob->verSeccionCod($cod));
if(!$f) outPreview(array('ok'=>false,'message'=>'Sección no encontrada.'));

$obs=new cSitios();
$sitio=mysqli_fetch_array($obs->verSitioCod($f['cod_sitio']));
if(!$sitio) outPreview(array('ok'=>false,'message'=>'Sitio no encontrado.'));

$cfg=msConfigSeccionSitio($sitio['slug_sitio'],$f['slug_seccion']);
$marker=isset($cfg['preview_marker'])?$cfg['preview_marker']:'';
if($marker==='') outPreview(array('ok'=>false,'message'=>'No hay preview configurado para esta sección.'));

$mp=$ob->verPaginaPrincipalSeccion($cod,$f['cod_sitio']);
$pagina=mysqli_fetch_array($mp);
$ruta=$pagina && trim($pagina['ruta_pagina'])!==''?trim($pagina['ruta_pagina']):'/';
$base=rtrim($sitio['url_sitio'],'/').'/';
$pageUrl=$ruta==='/'?$base:$base.ltrim($ruta,'/');

$html=fetchCms($pageUrl);
if($html===false && stripos($pageUrl,'http://localhost/')===0){
    $alt='http://127.0.0.1/'.ltrim(substr($pageUrl,strlen('http://localhost/')),'/');
    $html=fetchCms($alt);
    if($html!==false){ $pageUrl=$alt; $base=preg_replace('#[^/]+$#','',$alt); }
}
if($html===false) outPreview(array('ok'=>false,'message'=>'No se pudo leer '.$pageUrl.'. Verifica que http://localhost/metaroad-new/ esté funcionando.'));

$fragment=extractCmsMarker($html,$marker);
if($fragment===false) outPreview(array('ok'=>false,'message'=>'No se encontró el marcador de la sección '.$marker.' en METAROAD.'));

$remove=isset($cfg['preview_remove_marker'])?$cfg['preview_remove_marker']:'';
if($remove!=='') $fragment=removeCmsMarker($fragment,$remove);
$extra=isset($cfg['preview_extra_marker'])?$cfg['preview_extra_marker']:'';
if($extra!==''){
    $e=extractCmsMarker($html,$extra);
    if($e!==false) $fragment.=$e;
}
if(isset($cfg['preview_wrapper']) && trim($cfg['preview_wrapper'])!==''){
    $fragment=str_replace('{{CONTENT}}',$fragment,$cfg['preview_wrapper']);
}
$fragment=rewriteFragmentCms($fragment,$base);

$css='';
foreach((isset($cfg['preview_css'])?$cfg['preview_css']:array()) as $path){
    $url=absCms($base,$path);
    $ct=fetchCms($url);
    if($ct!==false) $css.="\n".rewriteCssCms($ct,$url)."\n";
}
$css.='\n:host{display:block;background:#071019;} .cms-site-section-canvas{font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;width:1199px;transform-origin:top left;position:relative;margin:0 auto;} .cms-site-section-canvas *{box-sizing:border-box;} .cms-header-preview-surface{height:90px!important;min-height:90px!important;}';

outPreview(array(
    'ok'=>true,
    'html'=>$fragment,
    'css'=>$css,
    'imports'=>isset($cfg['preview_imports'])?$cfg['preview_imports']:array(),
    'page_url'=>$pageUrl,
    'base_url'=>$base,
    'scope'=>$cfg['scope'],
    'slug_seccion'=>$f['slug_seccion'],
    'carpeta_uploads'=>$f['carpeta_uploads']
));
?>