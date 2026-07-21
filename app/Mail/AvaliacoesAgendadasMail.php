<?php

namespace App\Mail;

use App\Models\Colaborador;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AvaliacoesAgendadasMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Colaborador $colaborador,
        public Collection $avaliacoes,
        public bool $reagendada = false,
    ) {
        $this->colaborador->loadMissing(['gestor', 'setor']);
        $this->avaliacoes->loadMissing(['formulario']);
    }

    public function envelope(): Envelope
    {
        $prefixo = $this->reagendada ? 'Datas de avaliacoes atualizadas' : 'Avaliacoes agendadas';

        return new Envelope(
            subject: "{$prefixo}: {$this->colaborador->nome}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.avaliacoes-agendadas',
        );
    }
}
