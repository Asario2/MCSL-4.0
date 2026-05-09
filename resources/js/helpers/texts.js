// text.js

import { SD } from './dom';

/**
 * Smilies ersetzen
 */
export function replaceSmilies(text) {
    const smilies = {
        ";)": "wink",
        ":D": "biggrin",
        ":)": "smile",
        ":o": "surprised",
        ":shock:": "eek",
        ":?": "confused",
        " 8)": "cool",
        ":lol:": "lol",
        ":x": "mad",
        ":P": "razz",
        ":mrgreen:": "mrgreen",
        ":mcsl:":"mcsl",
        ":arrow:": "arrow",
        ":cry:": "cry",
        ":evil:": "evil",
        ":!:": "exclaim",
        ":{": "frown",
        ":idea:": "idea",
        ":|": "neutral",
        ":question:": "question",
        ":shy:": "redface",
        ":roll:": "rolleyes",
        ":(": "sad",
        "^^": "twisted",
        ":diso:": "disor",
        ":bankrob:": "bankrob",
        ":jesus:": "jesus",
        ":cyborg:": "cyborg",
        ":blade:": "blade",
        ":drugs:": "drugs",
        ":ying:": "ying",
        ":skull:": "skull",
        ":bomb:": "bomb",
        ":kiss:": "kiss",
        ":ugly:":"ugly",
        ":catch:": "catch",
        ":holy:": "holy"
    };

    for (const [key, value] of Object.entries(smilies)) {
        const escapedKey = key.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
        const regex = new RegExp(escapedKey, 'g');
        const img = `<img src="/images/smilies/icon_${value}.gif" class="inline-smiley" />`;
        text = text?.replace(regex, img);
    }

    return text;
}

/**
 * nl2br
 */
export function nl2br(str) {
    if (!str) return '';
    str = str.replace('%5B', '[').replace('%5D', ']');
    return str.replace(/\n/g, "<br />");
}

/**
 * Umlaut / Encoding Fix + Cleanup
 */
export function rumLaut(input, table = '') {
    let str = input;

    if (typeof str !== 'string') {
        if (str === null || str === undefined) return '';
        str = String(str);
    }

    // HTML cleanup
    str = str.replace(/<\/li>\s*<br\s*\/?>/gi, '</li>');
    str = str.replace(/<li>\s*<br\s*\/?>/gi, '<li>');
    str = str.replace(/<br\s*\/?>\s*(<(h2|h3|p)[^>]*>)/gi, '$1');
    str = str.replace(/(<\/(h2|h3|p)>)\s*<br\s*\/?>/gi, '$1');

    if (table === 'shortpoems' || table === 'didyouknow') {
        str = str.replace(/<p>/gi, '');
        str = str.replace(/<\/p>/gi, '');
        str = str.replace(/<br\s*\/?>/gi, '');
    }

    str = str.replace('%5B', '[').replace('%5D', ']');
    str = str.replace(/â€“/g, '-');

    // HTML decode
    if (typeof document !== 'undefined') {
        const txt = document.createElement("textarea");
        txt.innerHTML = str;
        str = txt.value;
    }

    const find = [
        /---/g, /ÃƒÅ“/g, /ÃƒÂ¼/g, /ÃƒÅ¸/g, /Ãƒ\?/g, /ÃƒÂ¤/g, /â€™/g, /Ã„/g,
        /Ãœ/gi, /Ã/g, /Ã¶/g, /Ã"Y/g, /Ã¼/g, /Ã¤/g, /ÃŸ/g, /âEUR¦/g, /ÃƒÂ¶/g,
        /Â§/gi, /Â©/gi, /Ã/gi
    ];

    const replace = [
        '<hr>', 'Ü', 'ü', 'ß', 'ß', 'ä', "'", 'Ä',
        'Ü', 'ß', 'ö', 'Ü', 'ü', 'ä', 'ß', '…', 'ö',
        '§', "©", 'ß'
    ];

    find.forEach((regex, i) => {
        str = str.replace(regex, replace[i]);
    });

    return replaceSmilies(str);
}

/**
 * Großschreibung + Umlautfix
 */
export function ucf(str) {
    if (str === "3ddrucker" || str === "3DDrucker") {
        return "3DDrucker";
    }

    str = rumLaut(str);

    const arr = str.split("_");

    return arr.map(val =>
        val.charAt(0).toUpperCase() + val.slice(1)
    ).join(" ");
}

/**
 * Entfernt %5B %5D
 */
export function remBrackets(str) {
    if (!str) return "";
    return str.replace('%5B', '[').replace('%5D', ']');
}

/**
 * Strip HTML Tags (Whitelist möglich)
 */
export function stripTags(text, allowed = []) {
    if (!text) return '';

    const allow = Array.isArray(allowed)
        ? allowed
        : (allowed || '').split(',').map(t => t.trim().toLowerCase());

    if (!allow.length) {
        return text.replace(/<\/?[^>]+>/gi, '');
    }

    return text.replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, (match, tag) =>
        allow.includes(tag.toLowerCase()) ? match : ''
    );
}

/**
 * Selection Helper (Editor)
 */
export const selectionHelper = {

    save() {
        if (typeof window === 'undefined') return;

        const sel = window.getSelection();
        if (sel && sel.rangeCount > 0) {
            this.savedRange = sel.getRangeAt(0).cloneRange();
        }
    },

    restore() {
        if (typeof window === 'undefined') return;

        const sel = window.getSelection();
        sel.removeAllRanges();

        if (this.savedRange) {
            sel.addRange(this.savedRange);
        }
    },
};