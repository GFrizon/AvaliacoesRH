/**
 * ThemeProvider — controla o tema claro/escuro/sistema da aplicação.
 *
 * - Padrão: 'light' quando o usuário nunca escolheu um tema.
 * - Persistência: localStorage (chave 'theme'), valores 'light' | 'dark' | 'system'.
 * - Quando 'system', segue prefers-color-scheme dinamicamente.
 * - Aplica data-theme no <html> e atualiza a meta theme-color.
 *
 * Este módulo é importado por resources/js/app.js, mas a aplicação inicial
 * do tema (para evitar flash) acontece via script inline no <head>
 * (ver resources/views/layouts/app.blade.php). Este módulo apenas assume
 * o controle depois do carregamento e expõe a API para o ThemeToggle.
 */

const STORAGE_KEY = 'theme';
const THEME_COLOR_LIGHT = '#f4f5f7';
const THEME_COLOR_DARK = '#121317';

function getStoredPreference() {
    try {
        return localStorage.getItem(STORAGE_KEY);
    } catch {
        return null;
    }
}

function setStoredPreference(value) {
    try {
        localStorage.setItem(STORAGE_KEY, value);
    } catch {
        /* localStorage indisponível (modo privado, etc.) — ignora */
    }
}

function systemPrefersDark() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function resolveEffectiveTheme(preference) {
    if (preference === 'dark') return 'dark';
    if (preference === 'light') return 'light';
    return systemPrefersDark() ? 'dark' : 'light';
}

function applyTheme(effectiveTheme) {
    document.documentElement.setAttribute('data-theme', effectiveTheme);

    let meta = document.querySelector('meta[name="theme-color"]');
    if (!meta) {
        meta = document.createElement('meta');
        meta.setAttribute('name', 'theme-color');
        document.head.appendChild(meta);
    }
    meta.setAttribute('content', effectiveTheme === 'dark' ? THEME_COLOR_DARK : THEME_COLOR_LIGHT);
}

export function getPreference() {
    return getStoredPreference() || 'light';
}

export function setPreference(preference) {
    setStoredPreference(preference);
    applyTheme(resolveEffectiveTheme(preference));
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { preference } }));
}

export function initTheme() {
    const preference = getPreference();
    applyTheme(resolveEffectiveTheme(preference));

    const media = window.matchMedia('(prefers-color-scheme: dark)');
    media.addEventListener('change', () => {
        if (getPreference() === 'system') {
            applyTheme(resolveEffectiveTheme('system'));
        }
    });
}

window.themeProvider = { getPreference, setPreference, initTheme };

initTheme();
