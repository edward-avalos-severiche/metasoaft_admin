<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}
if(!isset($_SESSION['cod_usuario'])){
    header('Location: index.php');
    exit;
}
?>
