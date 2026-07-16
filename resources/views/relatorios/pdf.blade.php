<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        h1 { font-size: 22px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Relatório de Avaliações</h1>
    <table>
        <thead>
            <tr>
                <th>Colaborador</th>
                <th>Setor</th>
                <th>Gestor</th>
                <th>Status</th>
                <th>Prazo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($avaliacoes as $avaliacao)
                <tr>
                    <td>{{ $avaliacao->colaborador->nome }}</td>
                    <td>{{ $avaliacao->colaborador->setor->nome }}</td>
                    <td>{{ $avaliacao->gestor->name }}</td>
                    <td>{{ $avaliacao->status->label() }}</td>
                    <td>{{ $avaliacao->data_limite->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
