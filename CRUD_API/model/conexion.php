<?php

abstract class Conexion {
    protected static $conexion = null;

    static function conectar(): PDO {
        try {
            self::$conexion = new PDO(
                "informix:host=host.docker.internal; service=9088; database=scifi; server=informix; protocol=onsoctcp;EnableScrollableCursors=1",
                "informix",
                "in4mix"
            );
            self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



        } catch (PDOException $e) {

            error_log("Error de conexión: " . $e->getMessage());
            self::$conexion = null;
            throw new Exception("No se pudo conectar a la base de datos.");
        }

        return self::$conexion;
    }

    public function ejecutar($sql, $params) {
        $conexion = self::conectar();
        $sentencia = $conexion->prepare($sql);
        $resultado = $sentencia->execute($params);
        $idInsertado = $conexion->lastInsertId();

        return [
            "resultado" => $resultado,
            "id" => $idInsertado
        ];
    }

    public function servir($sql, $params) {
        $conexion = self::conectar();
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute($params);
        $data = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        $datos = [];
        foreach ($data as $k => $v) {
            $datos[] = array_change_key_case($v, CASE_LOWER);
        }

        return $datos;
    }

    public static function getConexion(): PDO {
        return self::conectar();
    }
}

class DataBase extends Conexion {}

