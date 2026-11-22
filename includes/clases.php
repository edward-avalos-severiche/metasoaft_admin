<?php
class cUsuarios
{
    public function login($user_usuario,$password_usuario)
    {
        $obc=new conexion();
        $user_usuario=$obc->escapar($user_usuario);
        $password_usuario=$obc->escapar($password_usuario);

        $csql="
        SELECT *
        FROM crm_usuarios
        WHERE user_usuario='$user_usuario'
        AND password_usuario='$password_usuario'
        AND estado_usuario='1'
        LIMIT 1
        ";

        return $obc->consultar($csql);
    }

    public function contarUsuarios()
    {
        $csql="SELECT COUNT(*) total FROM crm_usuarios";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function crearUsuario($nom_usuario,$ape_usuario,$email_usuario,$user_usuario,$password_usuario)
    {
        $obc=new conexion();

        $nom_usuario=$obc->escapar($nom_usuario);
        $ape_usuario=$obc->escapar($ape_usuario);
        $email_usuario=$obc->escapar($email_usuario);
        $user_usuario=$obc->escapar($user_usuario);
        $password_usuario=md5($password_usuario);

        $csql="
        INSERT INTO crm_usuarios
        (
            nom_usuario,
            ape_usuario,
            email_usuario,
            user_usuario,
            password_usuario,
            tipo_usuario,
            estado_usuario
        )
        VALUES
        (
            '$nom_usuario',
            '$ape_usuario',
            '$email_usuario',
            '$user_usuario',
            '$password_usuario',
            'Administrador',
            '1'
        )
        ";

        return $obc->consultar($csql);
    }
}


class cSitios
{
    public function verSitios()
    {
        $csql="
        SELECT *
        FROM crm_sitios
        ORDER BY nom_sitio ASC
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function verSitioCod($cod_sitio)
    {
        $csql="
        SELECT *
        FROM crm_sitios
        WHERE cod_sitio='$cod_sitio'
        LIMIT 1
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function guardarSitio($cod_sitio,$nom_sitio,$slug_sitio,$url_sitio,$token_sitio,$estado_sitio)
    {
        $obc=new conexion();

        $nom_sitio=$obc->escapar($nom_sitio);
        $slug_sitio=$obc->escapar($slug_sitio);
        $url_sitio=rtrim(trim($url_sitio),'/').'/';
        $url_sitio=$obc->escapar($url_sitio);
        $token_sitio=$obc->escapar($token_sitio);

        if($cod_sitio==0){
            $csql="
            INSERT INTO crm_sitios
            (nom_sitio,slug_sitio,url_sitio,token_sitio,estado_sitio)
            VALUES
            ('$nom_sitio','$slug_sitio','$url_sitio','$token_sitio','$estado_sitio')
            ";
        }else{
            $csql="
            UPDATE crm_sitios
            SET
                nom_sitio='$nom_sitio',
                slug_sitio='$slug_sitio',
                url_sitio='$url_sitio',
                token_sitio='$token_sitio',
                estado_sitio='$estado_sitio'
            WHERE cod_sitio='$cod_sitio'
            ";
        }

        return $obc->consultar($csql);
    }
}


class cPaginas
{
    public function verPaginasSitio($cod_sitio)
    {
        $csql="
        SELECT *
        FROM web_paginas
        WHERE cod_sitio='$cod_sitio'
        AND eliminado_pagina='0'
        ORDER BY cod_pagina ASC
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function verPaginaCod($cod_pagina)
    {
        $csql="
        SELECT *
        FROM web_paginas
        WHERE cod_pagina='$cod_pagina'
        LIMIT 1
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function guardarPagina($cod_pagina,$cod_sitio,$nom_pagina,$slug_pagina,$titulo_pagina,$descripcion_pagina,$ruta_pagina,$estado_pagina)
    {
        $obc=new conexion();

        $nom_pagina=$obc->escapar($nom_pagina);
        $slug_pagina=$obc->escapar($slug_pagina);
        $titulo_pagina=$obc->escapar($titulo_pagina);
        $descripcion_pagina=$obc->escapar($descripcion_pagina);
        $ruta_pagina=$obc->escapar($ruta_pagina);

        if($cod_pagina==0){
            $csql="
            INSERT INTO web_paginas
            (
                cod_sitio,nom_pagina,slug_pagina,titulo_pagina,
                descripcion_pagina,ruta_pagina,estado_pagina,eliminado_pagina
            )
            VALUES
            (
                '$cod_sitio','$nom_pagina','$slug_pagina','$titulo_pagina',
                '$descripcion_pagina','$ruta_pagina','$estado_pagina','0'
            )
            ";
        }else{
            $csql="
            UPDATE web_paginas
            SET
                nom_pagina='$nom_pagina',
                slug_pagina='$slug_pagina',
                titulo_pagina='$titulo_pagina',
                descripcion_pagina='$descripcion_pagina',
                ruta_pagina='$ruta_pagina',
                estado_pagina='$estado_pagina',
                fecha_actualizacion=NOW()
            WHERE cod_pagina='$cod_pagina'
            ";
        }

        return $obc->consultar($csql);
    }
}


class cSecciones
{
    public function verSeccionesSitio($cod_sitio)
    {
        $csql="
        SELECT *
        FROM web_secciones
        WHERE cod_sitio='$cod_sitio'
        AND eliminado_seccion='0'
        ORDER BY global_seccion DESC,cod_seccion ASC
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function verSeccionCod($cod_seccion)
    {
        $csql="
        SELECT *
        FROM web_secciones
        WHERE cod_seccion='$cod_seccion'
        LIMIT 1
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function guardarSeccion(
        $cod_seccion,$cod_sitio,$nom_seccion,$slug_seccion,
        $carpeta_uploads,$contenido_seccion,$global_seccion,
        $orden_global,$estado_seccion
    ){
        $obc=new conexion();

        $nom_seccion=$obc->escapar($nom_seccion);
        $slug_seccion=$obc->escapar($slug_seccion);

        $carpeta_uploads=str_replace('\\','/',trim($carpeta_uploads));
        $carpeta_uploads=trim($carpeta_uploads,'/').'/';
        $carpeta_uploads=$obc->escapar($carpeta_uploads);

        $contenido_seccion=$obc->escapar($contenido_seccion);

        if($cod_seccion==0){
            $csql="
            INSERT INTO web_secciones
            (
                cod_sitio,nom_seccion,slug_seccion,carpeta_uploads,
                contenido_seccion,global_seccion,orden_global,
                estado_seccion,eliminado_seccion
            )
            VALUES
            (
                '$cod_sitio','$nom_seccion','$slug_seccion','$carpeta_uploads',
                '$contenido_seccion','$global_seccion','$orden_global',
                '$estado_seccion','0'
            )
            ";
        }else{
            $csql="
            UPDATE web_secciones
            SET
                nom_seccion='$nom_seccion',
                slug_seccion='$slug_seccion',
                carpeta_uploads='$carpeta_uploads',
                contenido_seccion='$contenido_seccion',
                global_seccion='$global_seccion',
                orden_global='$orden_global',
                estado_seccion='$estado_seccion',
                fecha_actualizacion=NOW()
            WHERE cod_seccion='$cod_seccion'
            ";
        }

        return $obc->consultar($csql);
    }

    public function guardarContenido($cod_seccion,$contenido_seccion)
    {
        $obc=new conexion();
        $contenido_seccion=$obc->escapar($contenido_seccion);

        $csql="
        UPDATE web_secciones
        SET
            contenido_seccion='$contenido_seccion',
            fecha_actualizacion=NOW()
        WHERE cod_seccion='$cod_seccion'
        ";

        return $obc->consultar($csql);
    }

    public function verSeccionesPagina($cod_pagina)
    {
        $csql="
        SELECT
            s.*,
            ps.orden_seccion
        FROM web_paginas_secciones ps
        INNER JOIN web_secciones s
        ON ps.cod_seccion=s.cod_seccion
        WHERE ps.cod_pagina='$cod_pagina'
        AND ps.estado_pagina_seccion='1'
        AND s.eliminado_seccion='0'
        ORDER BY ps.orden_seccion ASC
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function verPaginaPrincipalSeccion($cod_seccion,$cod_sitio)
    {
        $csql="
        SELECT p.*
        FROM web_paginas p
        LEFT JOIN web_paginas_secciones ps
        ON ps.cod_pagina=p.cod_pagina
        WHERE p.cod_sitio='$cod_sitio'
        AND p.estado_pagina='1'
        AND p.eliminado_pagina='0'
        AND
        (
            ps.cod_seccion='$cod_seccion'
            OR p.slug_pagina='inicio'
        )
        ORDER BY
            CASE WHEN ps.cod_seccion='$cod_seccion' THEN 0 ELSE 1 END,
            p.cod_pagina ASC
        LIMIT 1
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function asignarSeccionPagina($cod_pagina,$cod_seccion,$orden_seccion)
    {
        $csql="
        INSERT INTO web_paginas_secciones
        (cod_pagina,cod_seccion,orden_seccion,estado_pagina_seccion)
        VALUES
        ('$cod_pagina','$cod_seccion','$orden_seccion','1')
        ON DUPLICATE KEY UPDATE
            orden_seccion='$orden_seccion',
            estado_pagina_seccion='1'
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }

    public function quitarSeccionPagina($cod_pagina,$cod_seccion)
    {
        $csql="
        DELETE FROM web_paginas_secciones
        WHERE cod_pagina='$cod_pagina'
        AND cod_seccion='$cod_seccion'
        ";
        $obc=new conexion();
        return $obc->consultar($csql);
    }
}
?>
