// dom.js

/**
 * Holt erste ID aus URL
 */
export function CleanId() {
    if (typeof window === 'undefined') return '';

    const segments = window.location.pathname.split('/');

    for (let i = 0; i < segments.length; i++) {
        if (!isNaN(segments[i]) && segments[i].trim() !== "") {
            return segments[i];
        }
    }

    return null;
}

/**
 * Prefix Fix (p123 → P123)
 */
export function cc(img) {
    if (!img) return '';
    return img.replace(/^p(\d+)/, (match, p1) => 'P' + p1);
}

/**
 * Tabelle aus URL extrahieren
 */
export function CleanTable() {
    if (typeof window === 'undefined') return '';

    let segments = window.location.pathname.split('/');

    const remove = [
        "admin", "tables", "edit", "delete",
        "create", "show", "search", "home", "pictures"
    ];

    for (let i = 0; i < segments.length; i++) {
        if (remove.includes(segments[i]) || !isNaN(segments[i])) {
            segments.splice(i, 1);
            i--;
        }
    }

    segments = segments.join('').replace(/[[\]']/g, '');

    return segments.toLowerCase() || '';
}

/**
 * Alternative Table Cleaner (für Kommentare etc.)
 */
export function CleanTable_alt() {
    if (typeof window === 'undefined') return '';

    let segments = window.location.pathname.split('/');

    const remove = [
        "admin", "tables", "edit", "delete",
        "create", "show", "search", "home"
    ];

    for (let i = 0; i < segments.length; i++) {
        if (remove.includes(segments[i]) || !isNaN(segments[i])) {
            segments.splice(i, 1);
            i--;
        } else if (segments[i] === "pictures") {
            return "images";
        }
    }

    segments = segments.join('').replace(/[[\]']/g, '');

    return segments;
}

/**
 * Entfernt Teilstring aus URL
 */
export function CleanTab(rem) {
    if (typeof window === 'undefined') return '';

    const path = window.location.pathname;
    return path.replace(rem, '').replace("/", '');
}

/**
 * Subdomain / Projektkennung
 */
export function SD(pn = '') {
    if (typeof window === 'undefined') return '';

    let subb = window.location.hostname
        .replace(/^www\./, '')
        .split('.')[0];

    const pm = {
        ab: "Asarios Blog",
        dag: "Monika Dargies",
        mfx: "MarbleFX",
        mjs: "Mitja Schult",
        chh: "Rechtsanwalt Christian Henning"
    };

    switch (subb) {
        case "asario":
            subb = "ab";
            break;
        case "monikadargies":
            subb = "dag";
            break;
        case "marblefx":
            subb = "mfx";
            break;
        case "mjs":
            subb = "mjs";
            break;
        case "ra-c-henning":
            subb = "chh";
            break;
        case "localhost":
        case "test.mcs":
        case "241":
        case "217":
            subb = "ab";
            break;
        default:
            break;
    }

    if (!subb) subb = "ab";

    if (!pn) return subb;

    return pm[subb] || subb;
}