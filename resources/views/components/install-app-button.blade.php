{{--
    InstallAppButton — ação discreta de instalação da PWA.

    - Android / desktop compatíveis: captura `beforeinstallprompt` e
      oferece o prompt nativo do navegador.
    - iOS/iPadOS (sem prompt nativo): mostra instruções curtas de
      "Adicionar à Tela de Início", dispensáveis.
    - Já instalado (display-mode: standalone): não renderiza nada.
--}}
<div
    x-data="{
        visible: false,
        isIos: false,
        showIosHint: false,
        deferredPrompt: null,
        init() {
            if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
                return;
            }

            this.isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent) && !window.MSStream;

            if (this.isIos) {
                this.visible = !localStorage.getItem('install-hint-dismissed');
                return;
            }

            window.addEventListener('beforeinstallprompt', (event) => {
                event.preventDefault();
                this.deferredPrompt = event;
                this.visible = true;
            });

            window.addEventListener('appinstalled', () => {
                this.visible = false;
                this.deferredPrompt = null;
            });
        },
        async install() {
            if (this.isIos) {
                this.showIosHint = true;
                return;
            }
            if (!this.deferredPrompt) return;
            await this.deferredPrompt.prompt();
            this.deferredPrompt = null;
            this.visible = false;
        },
        dismiss() {
            this.visible = false;
            this.showIosHint = false;
            if (this.isIos) localStorage.setItem('install-hint-dismissed', '1');
        },
    }"
    x-show="visible"
    x-cloak
    style="display: none;"
>
    <button type="button" @click="install()" class="btn-ghost px-3">
        <i data-lucide="download" class="size-4" aria-hidden="true"></i>
        <span class="hidden sm:inline">Instalar aplicativo</span>
    </button>

    <template x-teleport="body">
        <div
            x-show="showIosHint"
            x-cloak
            style="display: none;"
            class="fixed inset-0 z-modal flex items-end justify-center bg-overlay p-4 sm:items-center"
            @click.self="showIosHint = false"
        >
            <div role="dialog" aria-modal="true" aria-label="Como instalar o aplicativo" class="app-card w-full max-w-sm p-5">
                <h2 class="text-sm font-semibold text-foreground">Adicionar à Tela de Início</h2>
                <ol class="mt-3 space-y-2 text-sm text-foreground-muted">
                    <li>1. Toque no ícone de compartilhar do Safari.</li>
                    <li>2. Escolha "Adicionar à Tela de Início".</li>
                    <li>3. Confirme em "Adicionar".</li>
                </ol>
                <button type="button" @click="dismiss()" class="btn-secondary mt-4 w-full">Entendi</button>
            </div>
        </div>
    </template>
</div>
