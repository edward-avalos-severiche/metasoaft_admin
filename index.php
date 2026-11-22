<?php
session_start();

if(isset($_SESSION['cod_usuario'])){
    header('Location: dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | Admin MetaSoft</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css">
<style>
body{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:radial-gradient(circle at top left,#183267,#0b1020 55%);
}
.login-box{
    width:min(430px,92vw);
    border:0;
    border-radius:24px;
}
</style>
</head>
<body>

<div class="card login-box shadow-lg">
    <div class="card-body p-5">

        <h2 class="fw-bold mb-1">Admin MetaSoft</h2>
        <p class="text-secondary mb-4">Gestión central de sitios web</p>

        <div id="msg"></div>

        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" id="user_usuario" class="form-control form-control-lg">
        </div>

        <div class="mb-4">
            <label class="form-label">Contraseña</label>
            <input type="password" id="password_usuario" class="form-control form-control-lg">
        </div>

        <button class="btn btn-primary btn-lg w-100" onclick="login();">
            Ingresar
        </button>

        <div class="text-center mt-3">
            <a href="crear-admin.php">Crear primer administrador</a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script>
function login()
{
    var parametros={
        "user_usuario":jQuery('#user_usuario').val(),
        "password_usuario":jQuery('#password_usuario').val(),
        "action":"login"
    };

    jQuery.ajax({
        data:parametros,
        url:'ajax.php',
        type:'post',
        beforeSend:function(){
            jQuery('#msg').html('<div class="alert alert-info">Verificando...</div>');
        },
        success:function(response){
            if(response=='ok'){
                window.location='dashboard.php';
            }else{
                jQuery('#msg').html('<div class="alert alert-danger">'+response+'</div>');
            }
        },
        error:function(xhr){
            var mensaje='Error interno del servidor.';

            if(xhr.responseText){
                mensaje=xhr.responseText;
            }

            jQuery('#msg').html(
                '<div class="alert alert-danger">'+mensaje+'</div>'
            );
        }
    });
}
</script>
</body>
</html>
