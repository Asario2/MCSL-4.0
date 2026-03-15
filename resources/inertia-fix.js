// // resources/js/inertia-fix.js
// console.log('🔧 Inertia Null Fix geladen - warte auf Inertia...');

// let fixApplied = false;

// const applyInertiaFix = () => {
//     // Prüfen ob Inertia bereits geladen ist
//     if (window.mergeDataIntoQueryString && !fixApplied) {
//         const originalFn = window.mergeDataIntoQueryString;

//         console.log('✅ Inertia gefunden - wende Fix an...');

//         window.mergeDataIntoQueryString = function(method, url, data, queryStringArrayFormat = 'brackets') {
//             try {
//                 // Debug-Ausgabe
//                 console.log('🔧 mergeDataIntoQueryString aufgerufen mit:', { method, url, data });

//                 // Null-Werte sicher handhaben
//                 if (data === null || data === undefined) {
//                     console.error('⚠️ Daten waren null/undefined, setze auf {}');
//                     data = {};
//                 }

//                 // Falls data ein Objekt ist, null-Werte entfernen
//                 if (data && typeof data === 'object') {
//                     const originalLength = Object.keys(data).length;
//                     data = Object.fromEntries(
//                         Object.entries(data).filter(([key, value]) => {
//                             if (value === null || value === undefined) {
//                                 console.error(`⚠️ Entferne null-Wert für Key: ${key}`);
//                                 return false;
//                             }
//                             return true;
//                         })
//                     );
//                     if (Object.keys(data).length !== originalLength) {
//                         console.log('🔧 Null-Werte entfernt, neue Daten:', data);
//                     }
//                 }

//                 const result = originalFn.call(this, method, url, data, queryStringArrayFormat);
//                 console.log('✅ mergeDataIntoQueryString erfolgreich');
//                 return result;

//             } catch (error) {
//                 console.error('❌ Fehler in mergeDataIntoQueryString:', error);
//                 // Fallback: Ohne Daten aufrufen
//                 return originalFn.call(this, method, url, {}, queryStringArrayFormat);
//             }
//         };

//         fixApplied = true;
//         console.log('✅ Inertia Null-Fix erfolgreich angewendet');

//     } else if (!window.mergeDataIntoQueryString) {
//         //
//         // Erneut versuchen in 100ms
//         setTimeout(applyInertiaFix, 100);
//     }
// };

// // Mehrfache Versuche - Inertia könnte asynchron geladen werden
// const maxAttempts = 10;
// let attempts = 0;

// const tryApplyFix = () => {
//     if (attempts < maxAttempts) {
//         attempts++;
//         applyInertiaFix();
//         if (!fixApplied) {
//             setTimeout(tryApplyFix, 200);
//         }
//     } else {
//         console.error('❌ Inertia Fix konnte nicht angewendet werden nach', maxAttempts, 'Versuchen');
//     }
// };

// // Starte den Fix-Versuch
// tryApplyFix();

// // Auch DOM Ready abwarten
// document.addEventListener('DOMContentLoaded', tryApplyFix);

// // Fallback: Event-Listener für Inertia-spezifische Events
// document.addEventListener('inertia:init', tryApplyFix);

// export default applyInertiaFix;
