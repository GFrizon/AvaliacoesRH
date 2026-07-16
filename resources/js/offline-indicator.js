/**
 * Indicador discreto de conexão. Não bloqueia a interface — apenas
 * informa o estado atual via um elemento com id="offline-indicator"
 * (ver components/topbar.blade.php / layouts/app.blade.php).
 */
export function initOfflineIndicator() {
    const el = document.getElementById('offline-indicator');
    if (!el) return;

    function update() {
        el.hidden = navigator.onLine;
    }

    window.addEventListener('online', update);
    window.addEventListener('offline', update);
    update();
}
