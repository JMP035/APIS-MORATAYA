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

$datos = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($datos) {
    $scifi = new Scifi();
    $resultado = $scifi->guardar($datos);
    
    if ($resultado) {
        http_response_code(201); 
        echo json_encode([
            'status' => 'success', 
            'message' => 'Elemento guardado correctamente',
            'data' => $scifi->obtener($datos['id']) 
        ]);
    } else {
        http_response_code(500); 
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error al guardar el elemento: ' . $scifi->getError()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => 'No se recibieron datos'
    ]);
}