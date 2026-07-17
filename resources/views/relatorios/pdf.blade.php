<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 11px; }
        .header { border-bottom: 3px solid #f5be00; padding-bottom: 14px; margin-bottom: 18px; }
        .brand { font-size: 18px; font-weight: 700; color: #0f4c81; }
        .subtitle { margin-top: 4px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 7px; text-align: left; vertical-align: top; }
        th { background: #eef3f8; color: #374151; font-size: 10px; text-transform: uppercase; }
        .status { font-weight: 700; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Bakof Tec · Avaliações RH</div>
        <div class="subtitle">Relatório gerado em {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Colaborador</th>
                <th>Unidade</th>
                <th>Setor</th>
                <th>Ciclo</th>
                <th>Gestor</th>
                <th>Status</th>
                <th>Prazo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($avaliacoes as $avaliacao)
                <tr>
                    <td>{{ $avaliacao->colaborador->nome }}</td>
                    <td>{{ $avaliacao->colaborador->unidade_negocio ?: 'Não informada' }}</td>
                    <td>{{ $avaliacao->colaborador->setor->nome }}</td>
                    <td>{{ $avaliacao->ciclo->label() }}</td>
                    <td>{{ $avaliacao->gestor->name }}</td>
                    <td class="status">{{ $avaliacao->status->label() }}</td>
                    <td>{{ $avaliacao->data_limite->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhuma avaliação encontrada para os filtros selecionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
