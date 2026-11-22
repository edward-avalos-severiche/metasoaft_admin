<?php

class conexion
{
    public static function conectar()
    {
        // En PHP 8 mysqli puede lanzar mysqli_sql_exception.
        // Desactivamos ese reporte para mantener tu estructura clásica
        // y controlar el error nosotros mismos.
        mysqli_report(MYSQLI_REPORT_OFF);

        $cn = mysqli_connect(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME
        );

        if(!$cn){
            http_response_code(500);
            die('Error de conexión MySQL: '.mysqli_connect_error());
        }

        mysqli_set_charset($cn,'utf8mb4');

        return $cn;
    }

    public function consultar($csql)
    {
        $cn = self::conectar();

        $resultado = mysqli_query($cn,$csql);

        if($resultado === false){
            http_response_code(500);
            die('Error SQL: '.mysqli_error($cn));
        }

        return $resultado;
    }

    public function escapar($valor)
    {
        $cn = self::conectar();
        return mysqli_real_escape_string($cn,$valor);
    }
}

?>
