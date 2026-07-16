/**
 * Registro do service worker. Falhas de registro não devem afetar o
 * funcionamento normal da aplicação (progressive enhancement).
 */
export function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) return;

    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch((error) => {
            console.warn('Falha ao registrar o service worker:', error);
        });
    });
}
