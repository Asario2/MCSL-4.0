import { apiGet, apiPost } from './api';

// ===== AUTH CACHE =====
let authCache = {
  user: null,
  loaded: false,
};

// ===== GET USER ID =====
export async function GetAuth() {
  try {
    const data = await apiGet('/GETUserID');
    return !!data;
  } catch (e) {
    console.error('Auth Fehler:', e);
    return false;
  }
}

// ===== FULL USER LOAD =====
export async function loadUser() {
  if (authCache.loaded) return authCache.user;

  try {
    const user = await apiGet('/api/user');
    authCache.user = user;
    authCache.loaded = true;
    return user;
  } catch (e) {
    authCache.user = null;
    authCache.loaded = true;
    return null;
  }
}

// ===== SILENT LOGIN =====
export async function silentLogin({ email, password }) {
  try {
    const data = await apiPost('/login-silent', {
      email,
      password
    });

    if (data?.user_id && data.user_id !== "7") {
      authCache.user = data;
      authCache.loaded = true;
      return data;
    }

    return null;
  } catch (e) {
    console.error('Silent Login fehlgeschlagen:', e);
    return null;
  }
}

// ===== CHECK + REDIRECT =====
export async function checkAuthAndRedirect() {
  try {
    const data = await apiGet('/GetAuth');
    return data === "true" ? "authenticated" : "login";
  } catch (e) {
    return "login";
  }
}

// ===== FORCE LOGIN =====
export async function requireAuth() {
  const isAuth = await GetAuth();

  if (!isAuth) {
    window.location.href = "/login";
    return false;
  }

  return true;
}

// ===== LOGOUT =====
export async function logout() {
  try {
    await apiPost('/logout');
    authCache.user = null;
    authCache.loaded = false;
    window.location.reload();
  } catch (e) {
    console.error('Logout Fehler:', e);
  }
}