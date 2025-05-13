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

$scifi = new Scifi();
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id === null) {
    $datos = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $confirmado = isset($datos['confirmar']) && $datos['confirmar'] === true;
    
    if (!$confirmado) {
        http_response_code(400); 
        echo json_encode([
            'status' => 'warning', 
            'message' => 'Se requiere confirmación para eliminar todos los elementos',
            'requireConfirmation' => true
        ]);
        exit;
    }
}

$resultado = $scifi->eliminar($id);

if ($resultado) {
    http_response_code(200); 
    echo json_encode([
        'status' => 'success', 
        'message' => 'Elemento' . ($id ? '' : 's') . ' eliminado' . ($id ? '' : 's') . ' correctamente'
    ]);
} else {
    http_response_code(500); 
    echo json_encode([
        'status' => 'error', 
        'message' => 'Error al eliminar: ' . $scifi->getError()
    ]);
}