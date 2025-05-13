<?php
require_once '../../model/scifi.php';
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
$datos = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($datos && $id) {
    $scifi = new Scifi();
    
    $resultado = $scifi->actualizar($id, $datos);
    
    if ($resultado) {
        http_response_code(200); 
        echo json_encode([
            'status' => 'success', 
            'message' => 'Elemento actualizado correctamente',
            'data' => $scifi->obtener($id) 
        ]);
    } else {
        http_response_code(500); 
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error al actualizar el elemento: ' . $scifi->getError()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Datos o ID no proporcionados'
    ]);
}