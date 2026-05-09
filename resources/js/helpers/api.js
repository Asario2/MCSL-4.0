import axios from 'axios';

// ===== AXIOS INSTANCE =====
const api = axios.create({
  baseURL: '/',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
  }
});

// CSRF automatisch setzen
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
  api.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

// ===== GENERIC METHODS =====
export async function apiGet(url, config = {}) {
  try {
    const res = await api.get(url, config);
    return res.data;
  } catch (e) {
    handleApiError(e);
    throw e;
  }
}

export async function apiPost(url, data = {}, config = {}) {
  try {
    const res = await api.post(url, data, config);
    return res.data;
  } catch (e) {
    handleApiError(e);
    throw e;
  }
}

export async function apiDelete(url, config = {}) {
  try {
    const res = await api.delete(url, config);
    return res.data;
  } catch (e) {
    handleApiError(e);
    throw e;
  }
}

// ===== ERROR HANDLING =====
function handleApiError(error) {
  if (!error.response) {
    console.error('Netzwerkfehler:', error);
    return;
  }

  const status = error.response.status;

  if (status === 401) {
    console.warn('Nicht eingeloggt');
  }

  if (status === 419) {
    console.warn('CSRF Token expired');
    location.reload();
  }

  if (status === 500) {
    console.error('Serverfehler:', error.response.data);
  }
}

export default api;
