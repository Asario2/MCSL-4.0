// rights.js

import { cache } from './cache';
import { apiGet, apiPost } from './api';
import { GetAuth } from './auth';

/**
 * Initiales Laden (optional)
 */
export async function initRights() {
  if (!cache.ready) {
    const [tables, rights] = await Promise.all([
      apiGet('/api/admin/tables'),
      apiGet('/api/user/rights')
    ]);

    cache.tables = Array.isArray(tables) ? tables : [];
    cache.rights = typeof rights === 'object' ? rights : {};
    cache.ready = true;
  }
}

/**
 * Einmaliges Laden (wie dein Original)
 */
export async function loadRightsOnce() {
  if (!cache.tables) {
    const tables = await apiGet('/api/admin/tables');
    cache.tables = Array.isArray(tables) ? tables : [];
  }

  if (!cache.rights) {
    const uid = await GetAuth();
    if (!uid) return 0;

    const rights = await apiGet('/api/user/rights/' + uid);

    if (rights === "0") return 0;

    cache.rights = typeof rights === 'object' ? rights : {};
  }
}

/**
 * Einzelrecht abrufen (mit Cache)
 */
export async function GetRights(right, table) {
  const key = `${right}_${table}`;

  // 🔥 FIX: auch 0 berücksichtigen!
  if (cache.batchRights[key] !== undefined) {
    return cache.batchRights[key];
  }

  try {
    const result = await apiGet(`/api/user/rights/des/${table}/${right}`);
    const val = result ?? 0;

    cache.batchRights[key] = val;
    return val;

  } catch (e) {
    console.error(`GetRights Fehler (${table}):`, e);
    return 0;
  }
}

/**
 * Batch Rechte (optimiert)
 */
export async function GetBatchRights(tables, rightType = 'view') {
  const key = `${rightType}_${[...tables].sort().join('_')}`;

  if (cache.batchRights[key] !== undefined) {
    return cache.batchRights[key];
  }

  try {
    const res = await apiPost('/api/user/batch-rights', {
      table: tables,
      right_type: rightType
    });

    const result = res || {};

    cache.batchRights[key] = result;
    return result;

  } catch (e) {
    console.error('BatchRights Fehler:', e);

    const fallback = {};
    tables.forEach(t => fallback[t] = 0);

    cache.batchRights[key] = fallback;
    return fallback;
  }
}

/**
 * Parallel-Fallback (wenn kein Batch Endpoint)
 */
export async function GetRightsParallel(tables, rightType = 'view') {
  try {
    const promises = tables.map(table =>
      GetRights(rightType, table).catch(() => 0)
    );

    const results = await Promise.all(promises);

    const map = {};
    tables.forEach((t, i) => {
      map[t] = results[i];
    });

    return map;

  } catch (e) {
    console.error('GetRightsParallel Fehler:', e);

    const fallback = {};
    tables.forEach(t => fallback[t] = 0);

    return fallback;
  }
}

/**
 * 🔥 WICHTIG: dein CheckTRights (mit Pending Cache)
 */
export async function CheckTRights(right, table) {
  const key = `${right}_${table}`;

  // Cache
  if (cache.batchRights[key] !== undefined) {
    return Promise.resolve(cache.batchRights[key]);
  }

  // Pending Request vorhanden?
  if (cache.pending[key]) {
    return cache.pending[key];
  }

  // Neuer Request
  const request = apiGet(`/api/user/rights/des/${table}/${right}`)
    .then(data => {
      const val = data ?? 0;

      cache.batchRights[key] = val;
      delete cache.pending[key];

      return val;
    })
    .catch(err => {
      delete cache.pending[key];
      console.error('CheckTRights Error:', err);
      return 0;
    });

  cache.pending[key] = request;

  return request;
}
