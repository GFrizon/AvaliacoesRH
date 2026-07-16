/**
 * Service Worker — Suite RH (Avaliações).
 *
 * Estratégias:
 * - Navegação (HTML): network-first. Em falha de rede, mostra
 *   /offline.html. NUNCA serve uma página privada em cache (evita
 *   dados desatualizados/sensíveis de avaliações, colaboradores etc.).
 * - Assets estáticos versionados (/build/*, /icons/*, favicons,
 *   manifest): cache-first, com cache nomeado por versão.
 * - Qualquer requisição não-GET (POST/PUT/DELETE — formulários,
 *   Livewire): sempre passa direto pela rede, nunca cacheada.
 * - Nenhuma resposta de rota autenticada/privada é armazenada.
 *
 * Atualização: bump CACHE_VERSION ao alterar a estratégia ou os
 * assets pré-cacheados; caches antigos são limpos no "activate".
 */

const CACHE_VERSION = 'v1';
const STATIC_CACHE = `static-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    OFFLINE_URL,
    '/favicon.svg',
    '/manifest.webmanifest',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key.startsWith('static-') && key !== STATIC_CACHE)
                    .map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

function isStaticAsset(url) {
    return (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname === '/favicon.svg' ||
        url.pathname === '/favicon.ico' ||
        url.pathname === '/manifest.webmanifest'
    );
}

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Nunca interceptar/cachear métodos que não sejam GET (formulários,
    // Livewire, ações destrutivas etc.).
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Apenas mesma origem — não interfere em CDNs/terceiros.
    if (url.origin !== self.location.origin) {
        return;
    }

    // Navegação de página (HTML): network-first, fallback para offline.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Assets estáticos versionados: cache-first.
    if (isStaticAsset(url)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;

                return fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(STATIC_CACHE).then((cache) => cache.put(request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // Demais requisições GET (ex.: rotas autenticadas/privadas): sempre
    // rede, sem cache — nenhum dado de colaboradores/avaliações é
    // persistido no dispositivo.
});
