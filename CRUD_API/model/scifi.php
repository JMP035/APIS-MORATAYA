<?php
require_once 'conexion.php';
class Scifi {
    private $conexion;
    private $error;
    
    public function __construct() {
        $this->conexion = new DataBase();
        $this->error = '';
    }
    
    public function getError() {
        return $this->error;
    }
    
    public function obtener($id = null) {
        try {
            if ($id === null) {
                $sql = "SELECT * FROM favoritos ORDER BY fav_fecha DESC";
                $results = $this->conexion->servir($sql, []);
                $transformed = [];
                foreach ($results as $r) {
                    $transformed[] = [
                        'id' => $r['fav_item'],
                        'api' => $r['fav_api'],
                        'type' => $r['fav_tipo'],
                        'dateadded' => $r['fav_fecha'],
                        'title' => $r['fav_titulo'] ?? $r['fav_item'],
                        'notes' => $r['fav_notas'] ?? '',
                        'rating' => $r['fav_rating'] ?? 0
                    ];
                }
                return $transformed;
            } else {
                $sql = "SELECT * FROM favoritos WHERE fav_item = ?";
                $results = $this->conexion->servir($sql, [$id]);
                if (count($results) > 0) {
                    $r = $results[0];
                    return [
                        'id' => $r['fav_item'],
                        'api' => $r['fav_api'],
                        'type' => $r['fav_tipo'],
                        'dateadded' => $r['fav_fecha'],
                        'title' => $r['fav_titulo'] ?? $r['fav_item'],
                        'notes' => $r['fav_notas'] ?? '',
                        'rating' => $r['fav_rating'] ?? 0
                    ];
                }
                return null;
            }
        } catch (Exception $e) {
            $this->error = "Error al obtener datos: " . $e->getMessage();
            return [];
        }
    }
    
    public function guardar($datos) {
        try {
            if (!isset($datos['id']) || !isset($datos['api']) || !isset($datos['type'])) {
                $this->error = "Faltan datos requeridos (id, api, type)";
                return false;
            }
            
            $sql = "SELECT COUNT(*) as count FROM favoritos WHERE fav_item = ?";
            $results = $this->conexion->servir($sql, [$datos['id']]);
            $existe = (count($results) > 0 && (int)$results[0]['count'] > 0);
            
            $title = $datos['title'] ?? $datos['id'];
            $notes = $datos['notes'] ?? '';
            $rating = isset($datos['rating']) ? (int)$datos['rating'] : 0;
            
            if ($existe) {
                $sql = "UPDATE favoritos SET 
                    fav_api = ?, 
                    fav_tipo = ?, 
                    fav_titulo = ?,
                    fav_notas = ?,
                    fav_rating = ?
                    WHERE fav_item = ?";
                $params = [
                    $datos['api'], 
                    $datos['type'], 
                    $title,
                    $notes,
                    $rating,
                    $datos['id']
                ];
            } else {
                $sql = "INSERT INTO favoritos 
                    (fav_item, fav_api, fav_tipo, fav_titulo, fav_notas, fav_rating) 
                    VALUES (?, ?, ?, ?, ?, ?)";
                $params = [
                    $datos['id'], 
                    $datos['api'], 
                    $datos['type'],
                    $title,
                    $notes,
                    $rating
                ];
            }
            
            $result = $this->conexion->ejecutar($sql, $params);
            return $result['resultado'];
        } catch (Exception $e) {
            $this->error = "Error al guardar: " . $e->getMessage();
            return false;
        }
    }
    
    public function eliminar($id = null) {
        try {
            if ($id === null) {
                $sql = "DELETE FROM favoritos";
                $result = $this->conexion->ejecutar($sql, []);
            } else {
                $sql = "DELETE FROM favoritos WHERE fav_item = ?";
                $result = $this->conexion->ejecutar($sql, [$id]);
            }
            return $result['resultado'];
        } catch (Exception $e) {
            $this->error = "Error al eliminar: " . $e->getMessage();
            return false;
        }
    }
    
    public function actualizar($id, $datos) {
        try {
            if (!$id) {
                $this->error = "ID no proporcionado";
                return false;
            }
            
            $sql = "SELECT COUNT(*) as count FROM favoritos WHERE fav_item = ?";
            $results = $this->conexion->servir($sql, [$id]);
            $existe = (count($results) > 0 && (int)$results[0]['count'] > 0);
            
            if (!$existe) {
                $this->error = "El elemento no existe";
                return false;
            }
            
            $campos = [];
            $valores = [];
            
            if (isset($datos['api'])) { 
                $campos[] = "fav_api = ?"; 
                $valores[] = $datos['api'];
            }
            
            if (isset($datos['type'])) { 
                $campos[] = "fav_tipo = ?"; 
                $valores[] = $datos['type'];
            }
            
            if (isset($datos['title'])) { 
                $campos[] = "fav_titulo = ?"; 
                $valores[] = $datos['title'];
            }
            
            if (isset($datos['notes'])) { 
                $campos[] = "fav_notas = ?"; 
                $valores[] = $datos['notes'];
            }
            
            if (isset($datos['rating'])) { 
                $campos[] = "fav_rating = ?"; 
                $valores[] = (int)$datos['rating'];
            }
            
            if (empty($campos)) {
                $this->error = "No se proporcionaron campos para actualizar";
                return false;
            }
            
            $sql = "UPDATE favoritos SET " . implode(", ", $campos) . " WHERE fav_item = ?";
            $valores[] = $id; 
            
            $result = $this->conexion->ejecutar($sql, $valores);
            return $result['resultado'];
        } catch (Exception $e) {
            $this->error = "Error al actualizar: " . $e->getMessage();
            return false;
        }
    }
    
    public function buscar($termino) {
        try {
            $sql = "SELECT * FROM favoritos WHERE fav_item LIKE ? OR fav_titulo LIKE ? ORDER BY fav_fecha DESC";
            $param = '%' . $termino . '%';
            $results = $this->conexion->servir($sql, [$param, $param]);
            
            $transformed = [];
            foreach ($results as $r) {
                $transformed[] = [
                    'id' => $r['fav_item'],
                    'api' => $r['fav_api'],
                    'type' => $r['fav_tipo'],
                    'dateadded' => $r['fav_fecha'],
                    'title' => $r['fav_titulo'] ?? $r['fav_item'],
                    'notes' => $r['fav_notas'] ?? '',
                    'rating' => $r['fav_rating'] ?? 0
                ];
            }
            
            return $transformed;
        } catch (Exception $e) {
            $this->error = "Error en la búsqueda: " . $e->getMessage();
            return [];
        }
    }
}