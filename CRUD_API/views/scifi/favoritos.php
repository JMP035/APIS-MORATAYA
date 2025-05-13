<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Favoritos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .content-wrapper {
            padding: 20px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-header {
            background: #263238;
            color: white;
        }
        .header {
            text-align: center;
            margin: 2rem 0;
            padding: 1rem;
            background-color: #263238;
            color: white;
            border-radius: 10px;
        }
        .filter-container {
            background: #f1f3f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
    <!-- Establecer variables globales -->
    <script>
        // Indicar que estamos en la página de favoritos
        const isFavorites = true;
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="/CRUD_API/index.php">APIS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle active" href="#" data-bs-toggle="dropdown">Universos Ficticios</a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="index.php?api=sw"><i class="bi bi-stars"></i> Star Wars</a></li>
                <li><a class="dropdown-item" href="index.php?api=bb"><i class="bi bi-flask"></i> Breaking Bad</a></li>
                <li><a class="dropdown-item" href="index.php?api=hp"><i class="bi bi-lightning"></i> Harry Potter</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item active" href="favoritos.php"><i class="bi bi-heart-fill"></i> Mis Favoritos</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    
    <div class="container">
        <div class="header">
            <h1><i class="bi bi-heart-fill text-danger me-2"></i>Mis Favoritos</h1>
        </div>
        
        <div class="content-wrapper">
            <!-- Filtros -->
            <div class="filter-container mb-4">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="filterInput" class="form-label">Buscar en favoritos:</label>
                        <input type="text" id="filterInput" class="form-control" placeholder="Título, notas...">
                    </div>
                    <div class="col-md-4">
                        <label for="apiFilter" class="form-label">Filtrar por universo:</label>
                        <select id="apiFilter" class="form-select">
                            <option value="todos">Todos los universos</option>
                            <option value="sw">Star Wars</option>
                            <option value="bb">Breaking Bad</option>
                            <option value="hp">Harry Potter</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-end">
                        <button id="clearFavs" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Limpiar todos
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-header">
                        <tr>
                            <th>No.</th>
                            <th>Universo</th>
                            <th>Categoría</th>
                            <th>Título</th>
                            <th>Notas</th>
                            <th>Calificación</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="favoritesTableBody">
                        <!-- Se llenará dinámicamente -->
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div id="favoritesPagination" class="mt-4"></div>
        </div>
    </div>
    
    <!-- Modal para editar favorito -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Editar favorito</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Título:</label>
                        <input type="text" id="editTitle" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas:</label>
                        <textarea id="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Calificación:</label>
                        <div id="ratingStars" class="text-center mb-2"></div>
                        <input type="range" id="rating" class="form-range" min="0" max="5" step="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="saveChanges" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Carga de scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- RUTA CORREGIDA: Ajusta esta ruta según la ubicación real del archivo -->
    <script src="../../src/js/scifi/scifi.js"></script>
</body>
</html>