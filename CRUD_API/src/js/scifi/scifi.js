// Variables globales
let favorites = [];
let currentApi = typeof currentApi !== 'undefined' ? currentApi : 'sw';

const apis = {
  sw: { url: 'https://swapi.dev/api/', name: 'Star Wars' },
  bb: { url: 'https://breakingbadapi.com/api/', name: 'Breaking Bad' },
  hp: { url: 'https://hp-api.onrender.com/api/', name: 'Harry Potter' }
};

// Notificaciones
function showNotification(message, type = 'success') {
  const container = document.getElementById('notification-container');
  if (!container) {
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

  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => notification.remove(), 300);
  }, 5000);
}

// Cargar favoritos
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

// Buscar datos
async function fetchData(api, category, searchTerm = '') {
  if (!apis[api]) {
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

    const spinner = document.getElementById('searchSpinner');
    if (spinner) spinner.classList.remove('d-none');

    const response = await fetch(url);
    let data = await response.json();

    if (api === 'sw') data = data.results || [];

    if (searchTerm && ((api === 'bb' && category !== 'characters') || api === 'hp')) {
      const term = searchTerm.toLowerCase();
      data = data.filter(item =>
        Object.values(item).some(val =>
          typeof val === 'string' && val.toLowerCase().includes(term)
        )
      );
    }

    if (spinner) spinner.classList.add('d-none');

    return data;
  } catch (error) {
    console.error('Error al buscar:', error);
    showNotification(`Error al obtener datos de ${apis[api].name}: ${error.message}`, 'danger');
    return [];
  }
}


document.addEventListener('DOMContentLoaded', async () => {
  await loadFavorites();

  const searchForm = document.getElementById('searchForm');
  if (searchForm) {
    searchForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const category = document.getElementById('categoriaSelect').value;
      const searchTerm = document.getElementById('searchInput').value;

      if (!category) {
        showNotification('Por favor, seleccione una categoría', 'warning');
        return;
      }

      const results = await fetchData(currentApi, category, searchTerm);
      displayResults(currentApi, results);
    });
  }

  const saveChangesBtn = document.getElementById('saveChanges');
  if (saveChangesBtn) saveChangesBtn.addEventListener('click', saveFavoriteChanges);

  const clearFavsBtn = document.getElementById('clearFavs');
  if (clearFavsBtn) clearFavsBtn.addEventListener('click', clearAllFavorites);

  const filterInput = document.getElementById('filterInput');
  if (filterInput) {
    filterInput.addEventListener('input', filterFavorites);
    const apiFilter = document.getElementById('apiFilter');
    if (apiFilter) apiFilter.addEventListener('change', filterFavorites);
  }

  if (document.getElementById('favoritesTableBody')) displayFavorites();
});
