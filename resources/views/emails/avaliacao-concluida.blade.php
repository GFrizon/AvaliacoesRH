@php($url = route('avaliacoes.show', $avaliacao))

<div style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#172033;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;background:#f4f7fb;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;border-collapse:collapse;background:#ffffff;border:1px solid #dbe3ef;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px;background:#123c69;color:#ffffff;">
                            <img src="{{ asset('images/bakoftec-logo.png') }}" alt="Bakof Tec" width="180" style="display:block;width:180px;max-width:70%;height:auto;margin:0 0 18px;">
                            <p style="margin:0 0 8px;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#c7e8ff;">Suíte RH - Avaliações</p>
                            <h1 style="margin:0;font-size:24px;line-height:1.25;">Avaliação concluída</h1>
                            <p style="margin:10px 0 0;font-size:15px;color:#edf8ff;">O resultado já está disponível para acompanhamento do RH.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 18px;font-size:16px;line-height:1.6;">
                                O gestor <strong>{{ $avaliacao->gestor->name }}</strong> concluiu a avaliação de
                                <strong>{{ $avaliacao->colaborador->nome }}</strong>.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;color:#61708a;width:34%;">Colaborador</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;font-weight:700;">{{ $avaliacao->colaborador->nome }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;color:#61708a;">Gestor</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;">{{ $avaliacao->gestor->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;color:#61708a;">Unidade</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;">{{ $avaliacao->colaborador->unidade_negocio ?: 'Não informada' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;color:#61708a;">Ciclo</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;">{{ $avaliacao->ciclo->label() }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;color:#61708a;">Formulário</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;">{{ $avaliacao->formulario->nome }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;color:#61708a;">Resultado</td>
                                    <td style="padding:12px 0;font-weight:700;color:{{ $avaliacao->efetivar ? '#047857' : '#b91c1c' }};">
                                        {{ $avaliacao->efetivar ? 'Efetivar' : 'Não efetivar' }}
                                    </td>
                                </tr>
                            </table>

                            @if ($avaliacao->observacoes_finais)
                                <div style="margin:0 0 24px;padding:18px 20px;background:#f8fafc;border:1px solid #e5edf6;border-radius:12px;">
                                    <p style="margin:0 0 10px;font-size:13px;letter-spacing:.06em;text-transform:uppercase;color:#61708a;font-weight:700;">Observacoes do gestor</p>
                                    <p style="margin:0;font-size:15px;line-height:1.6;color:#172033;white-space:pre-line;">{{ $avaliacao->observacoes_finais }}</p>
                                </div>
                            @endif

                            <a href="{{ $url }}" style="display:inline-block;background:#4736d4;color:#ffffff;padding:13px 18px;border-radius:10px;text-decoration:none;font-weight:700;">
                                Ver resultado completo
                            </a>

                            <p style="margin:22px 0 0;font-size:13px;line-height:1.5;color:#61708a;">
                                Se o botão não abrir, copie e cole este link no navegador:<br>
                                <a href="{{ $url }}" style="color:#123c69;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
