<?php
// Detectar URI actual para marcar enlaces activos
$current_uri = $_SERVER['REQUEST_URI'];
$is_api = strpos($current_uri, 'api=') !== false;
$current_api = '';

if ($is_api && preg_match('/api=([^&]+)/', $current_uri, $matches)) {
    $current_api = $matches[1];
}

$is_favoritos = strpos($current_uri, 'favoritos.php') !== false;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/CRUD_API/index.php">
      APIS UNIVERSOS PARALELOS
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav ms-auto">

        <!-- Menú de universos -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= $is_api || $is_favoritos ? 'active' : '' ?>" href="#" id="universoDropdown" data-bs-toggle="dropdown">
            Universos Ficticios
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item <?= $current_api === 'sw' ? 'active' : '' ?>" href="/CRUD_API/views/scifi/index.php?api=sw">
                <i class="bi bi-stars"></i> Star Wars
              </a>
            </li>
            <li>
              <a class="dropdown-item <?= $current_api === 'bb' ? 'active' : '' ?>" href="/CRUD_API/views/scifi/index.php?api=bb">
                <i class="bi bi-flask"></i> Breaking Bad
              </a>
            </li>
            <li>
              <a class="dropdown-item <?= $current_api === 'hp' ? 'active' : '' ?>" href="/CRUD_API/views/scifi/index.php?api=hp">
                <i class="bi bi-lightning"></i> Harry Potter
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item <?= $is_favoritos ? 'active' : '' ?>" href="/CRUD_API/views/scifi/favoritos.php">
                <i class="bi bi-heart-fill"></i> Mis Favoritos
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
