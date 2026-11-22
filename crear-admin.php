<?php
require_once 'includes/config.php';
require_once 'includes/conex.php';
require_once 'includes/clases.php';

$ob_u=new cUsuarios();
$matriz=$ob_u->contarUsuarios();
$fila=mysqli_fetch_array($matriz);
$existe=intval($fila['total'])>0;
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Crear administrador</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">

<h3 class="mb-4">Crear administrador</h3>

<?php if($existe){ ?>

<div class="alert alert-warning">
Ya existe un usuario. Los siguientes deben crearse desde el panel.
</div>
<a href="index.php" class="btn btn-primary">Ir al login</a>

<?php }else{ ?>

<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Nombre</label>
<input type="text" id="nom_usuario" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">Apellido</label>
<input type="text" id="ape_usuario" class="form-control">
</div>

<div class="col-12">
<label class="form-label">Correo</label>
<input type="email" id="email_usuario" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">Usuario</label>
<input type="text" id="user_usuario" class="form-control">
</div>

<div class="col-md-6">
<label class="form-label">Contraseña</label>
<input type="password" id="password_usuario" class="form-control">
</div>

<div class="col-12">
<button class="btn btn-primary" onclick="crearAdmin();">Crear administrador</button>
</div>

</div>

<?php } ?>

</div>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script>
function crearAdmin()
{
    var parametros={
        "nom_usuario":jQuery('#nom_usuario').val(),
        "ape_usuario":jQuery('#ape_usuario').val(),
        "email_usuario":jQuery('#email_usuario').val(),
        "user_usuario":jQuery('#user_usuario').val(),
        "password_usuario":jQuery('#password_usuario').val(),
        "action":"crearPrimerAdmin"
    };

    jQuery.ajax({
        data:parametros,
        url:'ajax.php',
        type:'post',
        success:function(response){
            if(response=='ok'){
                alert('Administrador creado.');
                window.location='index.php';
            }else{
                alert(response);
            }
        }
    });
}
</script>
</body>
</html>
