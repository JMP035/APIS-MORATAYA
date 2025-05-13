<?php
// Determinar qué API se está solicitando
$api = isset($_GET['api']) ? $_GET['api'] : 'sw';


include "../template/header.php";
// Mapeo de APIs a nombres completos
$apiNames = [
    'sw' => 'Star Wars',
    'bb' => 'Breaking Bad',
    'hp' => 'Harry Potter'
];

// Mapeo de APIs a íconos
$apiIcons = [
    'sw' => 'bi-stars',
    'bb' => 'bi-flask',
    'hp' => 'bi-lightning'
];

// Verificar si la API es válida
if (!array_key_exists($api, $apiNames)) {
    $api = 'sw'; // Valor predeterminado
}

// Mapeo de APIs a categorías
$apiCategories = [
    'sw' => [
        'people' => 'Personajes',
        'planets' => 'Planetas',
        'starships' => 'Naves',
        'films' => 'Películas'
    ],
    'bb' => [
        'characters' => 'Personajes',
        'episodes' => 'Episodios',
        'quotes' => 'Citas'
    ],
    'hp' => [
        'characters' => 'Personajes',
        'students' => 'Estudiantes',
        'house/gryffindor' => 'Casa Gryffindor',
        'house/slytherin' => 'Casa Slytherin',
        'spells' => 'Hechizos'
    ]
];

// Título de la página
$pageTitle = "Universo de " . $apiNames[$api];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?>API UNIVERSOS PARALELOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .content-wrapper {
            padding: 20px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1.page-title {
            text-align: center;
            margin: 20px 0;
            color: #263238;
            font-weight: 600;
            font-size: 2.2rem;
        }
        .api-icon {
            font-size: 2rem;
            margin-right: 10px;
            vertical-align: middle;
        }
    </style>
    <!-- IMPORTANTE: Definir la variable currentApi ANTES de cargar scifi.js -->
    <script>
        // Definir variable global que será leída por scifi.js
        window.currentApi = '<?= $api ?>'; 
    </script>
</head>
<body>
    <?php include_once '../CRUD_API/views/template/navbar.php'; ?>
    
    <div class="container">
        <h1 class="page-title">
            <i class="bi <?= $apiIcons[$api] ?> api-icon"></i>
            <?= $pageTitle ?>
        </h1>
        
        <div class="content-wrapper">
            <!-- Formulario de búsqueda -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="categoriaSelect" class="form-label">Categoría:</label>
                    <select id="categoriaSelect" class="form-select">
                        <option value="">Seleccione categoría...</option>
                        <?php foreach ($apiCategories[$api] as $value => $label): ?>
                            <option value="<?= $value ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="searchInput" class="form-label">Buscar:</label>
                    <div class="d-flex">
                        <input type="text" id="searchInput" class="form-control" placeholder="Escriba para buscar...">
                        <button id="searchBtn" class="btn btn-primary ms-2">Buscar</button>
                    </div>
                </div>
            </div>
            
            <!-- Indicador de carga -->
            <div id="searchSpinner" class="text-center my-5 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Buscando en el universo de <?= $apiNames[$api] ?>...</p>
            </div>
            
            <!-- Resultados de búsqueda -->
            <div id="resultsContainer">
                <div id="searchResults" class="row row-cols-1 row-cols-md-3 g-3"></div>
            </div>
        </div>
    </div>
    
    <!-- Modal para editar -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas personales:</label>
                        <textarea id="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Calificación (1-5):</label>
                        <input type="number" id="rating" class="form-control" min="1" max="5">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="saveChanges" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cargar scripts en el orden correcto -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Insertar el código JS directamente para evitar problemas de ruta -->
    <script>
    // Variables globales
    let favorites = [];
    // Usar la variable global definida en el head
    let currentApi = window.currentApi || 'sw';

    const apis = {
      sw: {url: 'https://swapi.dev/api/', name: 'Star Wars'},
      bb: {url: 'https://breakingbadapi.com/api/', name: 'Breaking Bad'},
      hp: {url: 'https://hp-api.onrender.com/api/', name: 'Harry Potter'}
    };

    // Configuración de notificaciones
    function showNotification(message, type = 'success') {
      const container = document.getElementById('notification-container');
      if (!container) {
        // Crear el contenedor si no existe
        const newContainer = document.createElement('div');
        newContainer.id = 'notification-container';
        newContainer.style.position = 'fixed';
        newContainer.style.top = '20px';
        newContainer.style.right = '20px';
        newContainer.style.zIndex = '9999';
        document.body.appendChild(newContainer);
      }
      
      const notification = document.createElement('div');
      notification.className = `alert alert-${type} alert-dismissible fade show`;
      notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      
      document.getElementById('notification-container').appendChild(notification);
      
      // Auto-cerrar después de 5 segundos
      setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
      }, 5000);
    }

    // Cargar favoritos al inicio
    async function loadFavorites() {
      try {
        const response = await fetch('../../controller/scifi/buscar.php');
        const result = await response.json();
        if (result.status === 'success') {
          favorites = result.data || [];
          console.log('Favoritos cargados:', favorites.length);
        } else {
          console.error('Error al cargar favoritos:', result.message);
          showNotification('Error al cargar favoritos: ' + result.message, 'danger');
        }
        return favorites;
      } catch (error) {
        console.error('Error al cargar favoritos:', error);
        showNotification('Error de conexión al cargar favoritos', 'danger');
        return [];
      }
    }

    // Buscar datos de API
    async function fetchData(api, category, searchTerm = '') {
      // Verificar si api es válida
      if (!apis[api]) {
        console.error(`API no válida: ${api}`);
        showNotification(`Error: API "${api}" no soportada`, 'danger');
        return [];
      }
      
      try {
        let url;
        
        if (api === 'hp' && category.startsWith('house/')) {
          url = `${apis.hp.url}characters/house/${category.split('/')[1]}`;
        } else {
          url = `${apis[api].url}${category}`;
          if (searchTerm) {
            if (api === 'sw') url += `?search=${encodeURIComponent(searchTerm)}`;
            else if (api === 'bb' && category === 'characters') url += `?name=${encodeURIComponent(searchTerm)}`;
          }
        }
        
        console.log(`Buscando en API ${api}, categoría ${category}, término: ${searchTerm || '(ninguno)'}`);
        console.log(`URL: ${url}`);
        
        // Mostrar spinner durante la carga
        const spinner = document.getElementById('searchSpinner');
        if (spinner) spinner.classList.remove('d-none');
        
        const response = await fetch(url);
        let data = await response.json();
        
        if (api === 'sw') data = data.results || [];
        
        // Filtrar manualmente si es necesario
        if (searchTerm && ((api === 'bb' && category !== 'characters') || api === 'hp')) {
          const term = searchTerm.toLowerCase();
          data = data.filter(item => Object.values(item).some(val => 
            typeof val === 'string' && val.toLowerCase().includes(term)));
        }
        
        // Ocultar spinner al terminar
        if (spinner) spinner.classList.add('d-none');
        
        console.log(`Resultados encontrados: ${data.length}`);
        return data;
      } catch (error) {
        console.error('Error:', error);
        // Ocultar spinner en caso de error
        const spinner = document.getElementById('searchSpinner');
        if (spinner) spinner.classList.add('d-none');
        
        showNotification(`Error al obtener datos de ${apis[api].name}: ${error.message}`, 'danger');
        return [];
      }
    }

    // Mostrar resultados de búsqueda
    function displayResults(api, results) {
      const container = document.getElementById('searchResults');
      if (!container) {
        console.error('No se encontró el contenedor de resultados');
        return;
      }
      
      container.innerHTML = '';
      
      if (results.length === 0) {
        container.innerHTML = '<div class="col-12 text-center"><p class="text-muted my-5">No se encontraron resultados</p></div>';
        return;
      }
      
      results.forEach(item => {
        let title, details = [], imgSrc = '';
        
        // Extraer información según la API
        if (api === 'sw') {
          title = item.name || item.title;
          if (item.birth_year) details.push(`Nacimiento: ${item.birth_year}`);
          if (item.climate) details.push(`Clima: ${item.climate}`);
          if (item.director) details.push(`Director: ${item.director}`);
          if (item.gender) details.push(`Género: ${item.gender}`);
          if (item.species) details.push(`Especie: ${Array.isArray(item.species) ? item.species.join(', ') : item.species}`);
        } else if (api === 'bb') {
          title = item.name || item.title || (item.quote ? `"${item.quote.substring(0, 20)}..."` : 'Ítem');
          if (item.img) imgSrc = item.img;
          if (item.nickname) details.push(`Apodo: ${item.nickname}`);
          if (item.status) details.push(`Estado: ${item.status}`);
          if (item.occupation) details.push(`Ocupación: ${Array.isArray(item.occupation) ? item.occupation.join(', ') : item.occupation}`);
        } else if (api === 'hp') {
          title = item.name || item.incantation || 'Ítem';
          if (item.image) imgSrc = item.image;
          if (item.house) details.push(`Casa: ${item.house}`);
          if (item.patronus) details.push(`Patronus: ${item.patronus}`);
          if (item.ancestry) details.push(`Linaje: ${item.ancestry}`);
          if (item.wand && item.wand.core) details.push(`Varita: ${item.wand.core}`);
        }
        
        const id = `${api}-${item.url?.split('/').pop() || item.char_id || item.id || Math.random().toString(36).substring(2, 9)}`;
        const type = document.getElementById('categoriaSelect').value.split('/').pop();
        const isFavorite = favorites.some(fav => fav.id === id);
        
        const card = document.createElement('div');
        card.className = 'col';
        
        let imgHtml = '';
        if (imgSrc && !imgSrc.includes('character-placeholder') && imgSrc !== 'N/A') {
          imgHtml = `<img src="${imgSrc}" class="card-img-top" alt="${title}" style="height: 180px; object-fit: cover;">`;
        } else {
          // Imagen placeholder según API
          const placeholderIcons = {
            'sw': 'bi-stars',
            'bb': 'bi-flask',
            'hp': 'bi-lightning'
          };
          
          imgHtml = `
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
              <i class="bi ${placeholderIcons[api] || 'bi-image'} fs-1 text-secondary"></i>
            </div>
          `;
        }
        
        card.innerHTML = `
          <div class="card h-100 ${isFavorite ? 'border-warning' : ''}">
            ${imgHtml}
            <div class="card-body">
              <h5 class="card-title text-truncate" title="${title}">${title}</h5>
              ${details.length > 0 ? `<ul class="list-group list-group-flush mb-3">
                ${details.map(d => `<li class="list-group-item">${d}</li>`).join('')}
              </ul>` : ''}
              <button class="btn ${isFavorite ? 'btn-danger rmv-fav' : 'btn-success add-fav'}" 
                data-id="${id}" data-api="${api}" data-title="${title}" data-type="${type}">
                <i class="bi bi-heart${isFavorite ? '-fill' : ''}"></i> 
                ${isFavorite ? 'Quitar' : 'Añadir'}
              </button>
            </div>
          </div>
        `;
        
        container.appendChild(card);
      });
      
      // Añadir eventos
      container.querySelectorAll('.add-fav').forEach(btn => 
        btn.addEventListener('click', addToFavorites));
      
      container.querySelectorAll('.rmv-fav').forEach(btn => 
        btn.addEventListener('click', removeFromFavorites));
    }

    // CRUD: Añadir favorito
    async function addToFavorites(event) {
      const btn = event.currentTarget;
      const id = btn.dataset.id;
      const api = btn.dataset.api;
      const title = btn.dataset.title;
      const type = btn.dataset.type;
      
      // Deshabilitar botón durante la operación
      btn.disabled = true;
      
      // Datos completos para enviar al backend
      const newFavorite = {
        id, 
        api, 
        type,
        title: title
      };
      
      try {
        const response = await fetch('../../controller/scifi/guardar.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(newFavorite)
        });
        
        const result = await response.json();
        if (result.status === 'success') {
          // Guardar la respuesta del servidor que incluye todos los datos
          if (result.data) {
            favorites.push(result.data);
          } else {
            // Fallback por si no devuelve datos completos
            favorites.push({
              id, api, title, type,
              notes: '',
              rating: 0,
              dateAdded: new Date().toISOString()
            });
          }
          
          // Actualizar UI
          btn.classList.replace('btn-success', 'btn-danger');
          btn.classList.replace('add-fav', 'rmv-fav');
          btn.innerHTML = '<i class="bi bi-heart-fill"></i> Quitar';
          
          btn.removeEventListener('click', addToFavorites);
          btn.addEventListener('click', removeFromFavorites);
          
          // Mostrar notificación
          showNotification(`Elemento "${title}" añadido a favoritos`, 'success');
        } else {
          showNotification(`Error: ${result.message}`, 'danger');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión', 'danger');
      } finally {
        // Habilitar botón nuevamente
        btn.disabled = false;
      }
    }

    // CRUD: Eliminar favorito
    async function removeFromFavorites(event) {
      const id = event.currentTarget.dataset.id;
      
      // Confirmar eliminación
      if (!confirm('¿Está seguro que desea eliminar este elemento de favoritos?')) {
        return;
      }
      
      try {
        const response = await fetch(`../../controller/scifi/eliminar.php?id=${id}`, {
          method: 'POST'
        });
        
        const result = await response.json();
        if (result.status === 'success') {
          // Encontrar elemento a eliminar para mostrar mensaje
          const itemToRemove = favorites.find(item => item.id === id);
          const itemName = itemToRemove ? (itemToRemove.title || itemToRemove.id) : id;
          
          // Actualizar lista local
          favorites = favorites.filter(item => item.id !== id);
          
          // Mostrar notificación
          showNotification(`Elemento "${itemName}" eliminado de favoritos`, 'success');
          
          // Actualizar UI
          const btn = event.currentTarget;
          btn.classList.replace('btn-danger', 'btn-success');
          btn.classList.replace('rmv-fav', 'add-fav');
          btn.innerHTML = '<i class="bi bi-heart"></i> Añadir';
          
          btn.removeEventListener('click', removeFromFavorites);
          btn.addEventListener('click', addToFavorites);
        } else {
          showNotification(`Error: ${result.message}`, 'danger');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión', 'danger');
      }
    }

    // Función vacía de guardado de cambios para evitar errores
    function saveFavoriteChanges() {
      console.log("Función de guardar cambios no implementada completamente");
      showNotification("Esta función no está implementada completamente", "warning");
    }

    // Al cargar el documento
    document.addEventListener('DOMContentLoaded', async function() {
      console.log('DOM cargado. Inicializando aplicación...');
      
      // Crear contenedor de notificaciones
      const notifContainer = document.createElement('div');
      notifContainer.id = 'notification-container';
      notifContainer.style.position = 'fixed';
      notifContainer.style.top = '20px';
      notifContainer.style.right = '20px';
      notifContainer.style.zIndex = '9999';
      document.body.appendChild(notifContainer);
      
      // Log de variables globales para debug
      console.log('API actual:', currentApi);
      
      // Cargar favoritos
      await loadFavorites();
      
      // Configurar el botón de búsqueda
      document.getElementById('searchBtn').addEventListener('click', async function() {
        const categoria = document.getElementById('categoriaSelect').value;
        const termino = document.getElementById('searchInput').value;
        
        if (!categoria) {
          showNotification('Por favor, seleccione una categoría', 'warning');
          return;
        }
        
        try {
          console.log(`Buscando en ${currentApi}, categoría ${categoria}, término: ${termino || '(ninguno)'}`);
          const results = await fetchData(currentApi, categoria, termino);
          displayResults(currentApi, results);
        } catch (error) {
          console.error('Error en la búsqueda:', error);
          showNotification('Error al realizar la búsqueda: ' + error.message, 'danger');
        }
      });
      
      console.log('Inicialización completada');
    });
    </script>
</body>
</html>