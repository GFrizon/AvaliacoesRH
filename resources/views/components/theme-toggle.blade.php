{{--
    Seletor de tema — Claro / Escuro / Sistema.
    Usa Alpine.js para o menu e delega a aplicação real do tema para
    resources/js/theme.js (window.themeProvider), que já cuida de
    localStorage, prefers-color-scheme e do atributo data-theme.
--}}
<div
    x-data="{
        open: false,
        preference: (window.themeProvider ? window.themeProvider.getPreference() : 'light'),
        options: [
            { value: 'light', label: 'Claro', icon: 'sun' },
            { value: 'dark', label: 'Escuro', icon: 'moon' },
            { value: 'system', label: 'Sistema', icon: 'monitor' },
        ],
        choose(value) {
            this.preference = value;
            window.themeProvider?.setPreference(value);
            this.open = false;
            this.$nextTick(() => window.renderIcons?.());
        },
        currentIcon() {
            return this.options.find(o => o.value === this.preference)?.icon || 'sun';
        },
    }"
    @keydown.escape.window="open = false"
    class="relative"
>
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open.toString()"
        aria-haspopup="menu"
        class="btn-ghost inline-flex items-center gap-2 px-3"
    >
        <i data-lucide="sun" x-show="currentIcon() === 'sun'" class="size-4" aria-hidden="true"></i>
        <i data-lucide="moon" x-show="currentIcon() === 'moon'" class="size-4" aria-hidden="true"></i>
        <i data-lucide="monitor" x-show="currentIcon() === 'monitor'" class="size-4" aria-hidden="true"></i>
        <span class="hidden sm:inline">Tema</span>
        <i data-lucide="chevron-down" class="size-3.5" aria-hidden="true"></i>
    </button>

    <div
        x-show="open"
        x-transition.duration.120ms
        @click.outside="open = false"
        role="menu"
        aria-label="Selecionar tema"
        class="absolute right-0 z-dropdown mt-2 w-40 overflow-hidden rounded-md border border-border bg-surface py-1 shadow-md"
        style="display: none;"
    >
        <template x-for="option in options" :key="option.value">
            <button
                type="button"
                role="menuitemradio"
                :aria-checked="(preference === option.value).toString()"
                @click="choose(option.value)"
                class="flex w-full items-center gap-2 px-3 py-2 text-sm text-foreground transition-colors hover:bg-surface-hover"
                :class="preference === option.value ? 'text-primary font-medium' : ''"
            >
                <i :data-lucide="option.icon" class="size-4" aria-hidden="true"></i>
                <span x-text="option.label"></span>
            </button>
        </template>
    </div>
</div>
