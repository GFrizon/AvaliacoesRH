<?php

namespace App\Mail;

use App\Models\Avaliacao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AvaliacaoConcluidaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Avaliacao $avaliacao)
    {
        $this->avaliacao->loadMissing(['colaborador.setor', 'formulario', 'gestor', 'respostas.pergunta']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Avaliação concluída: {$this->avaliacao->colaborador->nome} ({$this->avaliacao->ciclo->label()})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.avaliacao-concluida',
        );
    }
}
