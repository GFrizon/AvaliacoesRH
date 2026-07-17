@php($url = route('avaliacoes.index'))

<div style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#172033;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;background:#f4f7fb;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;border-collapse:collapse;background:#ffffff;border:1px solid #dbe3ef;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px;background:#0f4c81;color:#ffffff;">
                            <img src="{{ asset('images/bakoftec-logo.png') }}" alt="Bakof Tec" width="180" style="display:block;width:180px;max-width:70%;height:auto;margin:0 0 18px;">
                            <p style="margin:0 0 8px;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#bfe8ff;">Suíte RH - Avaliações</p>
                            <h1 style="margin:0;font-size:24px;line-height:1.25;">Avaliações agendadas</h1>
                            <p style="margin:10px 0 0;font-size:15px;color:#e6f7ff;">Novos ciclos foram programados para acompanhamento.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 18px;font-size:16px;line-height:1.6;">
                                Olá, {{ $colaborador->gestor->name }}. O RH cadastrou o colaborador
                                <strong>{{ $colaborador->nome }}</strong> e as avaliações abaixo foram agendadas para você.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;color:#61708a;width:34%;">Colaborador</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;font-weight:700;">{{ $colaborador->nome }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;color:#61708a;">Cargo</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;">{{ $colaborador->cargo }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;color:#61708a;">Unidade</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #e5edf6;">{{ $colaborador->unidade_negocio ?: 'Não informada' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;color:#61708a;">Setor</td>
                                    <td style="padding:12px 0;">{{ $colaborador->setor->nome }}</td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 0 24px;border:1px solid #e5edf6;border-radius:12px;overflow:hidden;">
                                <tr>
                                    <th align="left" style="padding:12px 14px;background:#f0f4fa;color:#3d4b63;font-size:13px;">Ciclo</th>
                                    <th align="left" style="padding:12px 14px;background:#f0f4fa;color:#3d4b63;font-size:13px;">Formulário</th>
                                    <th align="left" style="padding:12px 14px;background:#f0f4fa;color:#3d4b63;font-size:13px;">Prazo</th>
                                </tr>
                                @foreach($avaliacoes as $avaliacao)
                                    <tr>
                                        <td style="padding:12px 14px;border-top:1px solid #e5edf6;">{{ $avaliacao->ciclo->label() }}</td>
                                        <td style="padding:12px 14px;border-top:1px solid #e5edf6;">{{ $avaliacao->formulario->nome }}</td>
                                        <td style="padding:12px 14px;border-top:1px solid #e5edf6;font-weight:700;">{{ $avaliacao->data_limite->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </table>

                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#61708a;">
                                Quando cada ciclo chegar ao prazo, o sistema enviará o alerta de preenchimento normalmente.
                            </p>

                            <a href="{{ $url }}" style="display:inline-block;background:#4736d4;color:#ffffff;padding:13px 18px;border-radius:10px;text-decoration:none;font-weight:700;">
                                Ver avaliações
                            </a>

                            <p style="margin:22px 0 0;font-size:13px;line-height:1.5;color:#61708a;">
                                Se o botão não abrir, copie e cole este link no navegador:<br>
                                <a href="{{ $url }}" style="color:#0f4c81;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
