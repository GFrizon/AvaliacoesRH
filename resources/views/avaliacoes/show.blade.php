@extends('layouts.app')

@section('content')
<div class="evaluation-hero mb-6">
    <div class="flex flex-col justify-between gap-6 md:flex-row">
        <div>
            <p class="page-kicker">Avaliação de desempenho</p>
            <h2 class="page-title mt-2">{{ $avaliacao->colaborador->nome }}</h2>
            <p class="mt-2 text-foreground-muted">{{ $avaliacao->colaborador->cargo }} - {{ $avaliacao->colaborador->setor->nome }}</p>
            <div class="mt-4 flex flex-wrap gap-2 text-sm">
                <span class="rounded-full bg-sky-50 px-3 py-1 font-semibold text-sky-700">{{ $avaliacao->ciclo->label() }}</span>
                <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-600">{{ $avaliacao->formulario->tipo->label() }}</span>
            </div>
        </div>
        <div class="evaluation-meta md:text-right">
            <span>Admissão: {{ optional($avaliacao->colaborador->data_admissao)->format('d/m/Y') ?? 'Não informado' }}</span>
            <span>Responsável: {{ $avaliacao->gestor->name }}</span>
            <span>Formulário: {{ $avaliacao->formulario->nome }}</span>
            <span>Prazo: {{ $avaliacao->data_limite->format('d/m/Y') }}</span>
            @if (auth()->user()->isRh() && in_array($avaliacao->status->value, ['agendada', 'pendente', 'concluida'], true))
                <form method="post" action="{{ route('avaliacoes.reenviar-email', $avaliacao) }}" class="mt-3">
                    @csrf
                    <button class="btn-secondary w-full justify-center md:w-auto">
                        <i data-lucide="mail" class="size-4"></i>
                        Reenviar e-mail
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@if (auth()->user()->isGestor() && $avaliacao->status->value === 'pendente')
    @php($perguntasAtivas = $avaliacao->formulario->perguntas->where('is_active', true))
    <form method="post" action="{{ route('avaliacoes.submit', $avaliacao) }}" class="evaluation-form" x-data="{ answered: 0, total: {{ $perguntasAtivas->count() }}, update() { this.answered = new Set([...$el.querySelectorAll('[data-answer]')].filter((field) => field.type === 'radio' ? field.checked : field.value.trim() !== '').map((field) => field.name)).size } }">
        @csrf
        <div class="evaluation-progress">
            <div class="mb-3 flex justify-between text-sm">
                <span>Progresso</span>
                <span x-text="`${answered}/${total}`"></span>
            </div>
            <div class="progress-track">
                <div class="progress-value" :style="`width: ${total ? (answered / total) * 100 : 0}%`"></div>
            </div>
        </div>

        @foreach ($perguntasAtivas as $index => $pergunta)
            <section class="question-card">
                <label class="block">
                    <span class="question-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="question-title">{{ $pergunta->titulo }}</span>
                    @if ($pergunta->descricao)
                        <span class="question-description">{{ $pergunta->descricao }}</span>
                    @endif

                    @switch($pergunta->tipo->value)
                        @case('texto_longo')
                            <textarea name="respostas[{{ $pergunta->id }}]" rows="3" @input="update" data-answer class="evaluation-input"></textarea>
                            @break
                        @case('escala_1_5')
                            <div class="choice-grid mt-4 grid grid-cols-5 gap-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label class="choice-option">
                                        <input type="radio" name="respostas[{{ $pergunta->id }}]" value="{{ $i }}" class="sr-only" @change="update" data-answer>
                                        {{ $i }}
                                    </label>
                                @endfor
                            </div>
                            @break
                        @case('sim_nao')
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <label class="choice-option choice-positive"><input type="radio" name="respostas[{{ $pergunta->id }}]" value="sim" class="sr-only" @change="update" data-answer>Sim</label>
                                <label class="choice-option choice-negative"><input type="radio" name="respostas[{{ $pergunta->id }}]" value="nao" class="sr-only" @change="update" data-answer>Não</label>
                            </div>
                            @break
                        @default
                            <input name="respostas[{{ $pergunta->id }}]" type="text" @input="update" data-answer class="evaluation-input">
                    @endswitch
                </label>
            </section>
        @endforeach

        <section class="question-card question-card-final">
            <label class="block">
                <span class="question-title">Observacoes finais</span>
                <textarea name="observacoes_finais" rows="3" class="evaluation-input"></textarea>
            </label>
            <div class="mt-5">
                <p class="mb-3 question-title">Efetivar colaborador?</p>
                <div class="grid grid-cols-2 gap-2">
                    <label class="choice-option choice-positive"><input required type="radio" name="efetivar" value="1" class="sr-only">Sim</label>
                    <label class="choice-option choice-negative"><input required type="radio" name="efetivar" value="0" class="sr-only">Não</label>
                </div>
            </div>
        </section>

        <div class="evaluation-submit">
            <button class="btn-primary">Enviar avaliação</button>
        </div>
    </form>
@else
    <section class="app-card rounded-2xl p-5">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
            <div>
                <h3 class="font-semibold">Resultado da avaliação</h3>
                <p class="mt-2 text-sm text-zinc-400">Status: {{ $avaliacao->status->label() }}</p>
            </div>
            @if (! is_null($avaliacao->efetivar))
                <span class="h-fit rounded-full px-3 py-1 text-sm {{ $avaliacao->efetivar ? 'bg-green-500/10 text-green-300' : 'bg-red-500/10 text-red-300' }}">
                    {{ $avaliacao->efetivar ? 'Efetivar' : 'Não efetivar' }}
                </span>
            @endif
        </div>

        @if ($avaliacao->status->value === 'cancelada')
            <div class="mt-5 rounded-lg border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                {{ $avaliacao->motivo_cancelamento ?: 'Avaliação cancelada.' }}
            </div>
        @endif

        <div class="mt-6 space-y-4">
            @forelse ($avaliacao->formulario->perguntas as $pergunta)
                @php($resposta = $avaliacao->respostas->firstWhere('pergunta_id', $pergunta->id))
                <article class="rounded-lg border border-white/10 bg-zinc-950/60 p-4">
                    <p class="font-medium">{{ $pergunta->titulo }}</p>
                    @if ($pergunta->descricao)
                        <p class="mt-1 text-sm text-zinc-500">{{ $pergunta->descricao }}</p>
                    @endif
                    <p class="mt-3 whitespace-pre-line text-sm text-zinc-300">{{ data_get($resposta?->valor, 'value') ?: 'Sem resposta.' }}</p>
                </article>
            @empty
                <p class="text-sm text-zinc-400">Este formulário não possui perguntas.</p>
            @endforelse
        </div>

        <div class="mt-6 rounded-lg border border-white/10 bg-zinc-950/60 p-4">
            <p class="font-medium">Observacoes finais</p>
            <p class="mt-2 whitespace-pre-line text-sm text-zinc-300">{{ $avaliacao->observacoes_finais ?: 'Sem observações finais.' }}</p>
        </div>
    </section>
@endif
@endsection

