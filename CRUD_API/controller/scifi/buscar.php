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

try {
    $scifi = new Scifi();

    $id = $_GET['id'] ?? null;
    $termino = $_GET['termino'] ?? null;

    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $porPagina = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;

    $ordenarPor = $_GET['ordenar'] ?? 'dateadded';
    $direccion = $_GET['direccion'] ?? 'desc';

    if ($id) {
        $resultado = $scifi->obtener($id);

        if ($resultado) {
            echo json_encode([
                'status' => 'success',
                'data' => [$resultado]
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Elemento no encontrado'
            ]);
        }
    } else {
        $data = $termino ? $scifi->buscar($termino) : $scifi->obtener();

        if (!is_array($data)) {
            $data = [];
        }

        usort($data, function ($a, $b) use ($ordenarPor, $direccion) {
            $compare = 0;
            if ($ordenarPor === 'rating') {
                $compare = $a['rating'] <=> $b['rating'];
            } else if ($ordenarPor === 'title') {
                $compare = strcasecmp($a['title'], $b['title']);
            } else {
                $compare = strtotime($a['dateadded']) <=> strtotime($b['dateadded']);
            }

            return $direccion === 'desc' ? -$compare : $compare;
        });

        $total = count($data);
        $totalPaginas = ceil($total / $porPagina);
        $inicio = ($pagina - 1) * $porPagina;
        $dataPaginada = array_slice($data, $inicio, $porPagina);

        echo json_encode([
            'status' => 'success',
            'data' => $dataPaginada,
            'paginacion' => [
                'total' => $total,
                'porPagina' => $porPagina,
                'paginaActual' => $pagina,
                'totalPaginas' => $totalPaginas
            ]
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
