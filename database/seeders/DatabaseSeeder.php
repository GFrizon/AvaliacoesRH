<?php

namespace Database\Seeders;

use App\Enums\FormularioTipo;
use App\Enums\PerguntaTipo;
use App\Enums\UserRole;
use App\Models\Empresa;
use App\Models\Formulario;
use App\Models\Pergunta;
use App\Models\Setor;
use App\Models\UnidadeNegocio;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $empresa = Empresa::updateOrCreate(
            ['documento' => '00.000.000/0001-00'],
            ['nome' => 'Bakof Tec'],
        );

        User::updateOrCreate(
            ['email' => 'rh@bakof.com.br'],
            [
                'empresa_id' => $empresa->id,
                'name' => 'RH Bakof',
                'role' => UserRole::Rh,
                'phone' => null,
                'is_active' => true,
                'password' => Hash::make('Bakof@2026'),
            ],
        );

        Setor::updateOrCreate(
            ['empresa_id' => $empresa->id, 'nome' => 'Operações'],
            ['descricao' => null, 'is_active' => true],
        );

        Setor::updateOrCreate(
            ['empresa_id' => $empresa->id, 'nome' => 'Produto'],
            ['descricao' => null, 'is_active' => true],
        );

        UnidadeNegocio::updateOrCreate(
            ['empresa_id' => $empresa->id, 'nome' => 'Bakof Tec'],
            ['is_active' => true],
        );

        UnidadeNegocio::updateOrCreate(
            ['empresa_id' => $empresa->id, 'nome' => 'Bakof Matriz'],
            ['is_active' => true],
        );

        $this->criarFormularios($empresa);
    }

    private function criarFormularios(Empresa $empresa): void
    {
        $this->criarFormulario(
            $empresa,
            FormularioTipo::Administrativo,
            'Avaliação de Desempenho - Modelo Geral ADM',
            'Modelo para áreas administrativas. Aplicado nos ciclos de 90 dias, 6 meses e 1 ano.',
            [
                ['Pontualidade e frequência', 'Como se encontra a organização pessoal do colaborador quanto à rotina laboral? Está conseguindo cumprir os horários mantendo sua frequência?'],
                ['Produção e desempenho', 'O colaborador está correspondendo na prática aos objetivos propostos para sua função?'],
                ['Relacionamento com colegas', 'Como está o relacionamento do colaborador com os colegas?'],
                ['Disciplina', 'O colaborador respeita as normas? Está alinhado com os valores da empresa?'],
                ['Emocional', 'O colaborador demonstra autocontrole, empatia, motivação e assimilação de seus sentimentos?'],
                ['Criatividade e capacidade de realização', 'O colaborador demonstra proatividade para criar, planejar, executar e manter constância no planejado?'],
                ['Pontos fortes do colaborador', 'Justifique.'],
                ['Sugestões de melhorias para o colaborador', 'Justifique.'],
                ['Feedback do colaborador quanto aos pontos abordados na avaliação', 'Como o colaborador se vê?'],
            ],
        );

        $this->criarFormulario(
            $empresa,
            FormularioTipo::ComercialEngenharia,
            'Formulário de Avaliação - Comercial e Engenharia',
            'Modelo para Comercial e Engenharia. Aplicado nos ciclos de 90 dias, 6 meses e 1 ano.',
            [
                ['Presença no trabalho - Como eu me vejo?', 'O colaborador precisa organizar a vida pessoal para garantir a sua frequência constante.'],
                ['Presença no trabalho - Como meu gestor me vê?', 'Histórico de presença.'],
                ['Trabalho em equipe - Como eu me vejo?', 'Trabalhar em equipe, saber ouvir e construir parcerias internas e externas.'],
                ['Trabalho em equipe - Como meu gestor me vê?', 'Habilidade de negociação, trabalho em equipe e parcerias.'],
                ['Emocional - Como eu me vejo?', 'Autocontrole, empatia, autoconhecimento e motivação.'],
                ['Emocional - Como meu gestor me vê?', 'Assimilação de sentimentos para acolher e preservar o público externo.'],
                ['Criatividade e realização - Como eu me vejo?', 'Criar, planejar, executar e manter constância no planejado.'],
                ['Criatividade e realização - Como meu gestor me vê?', 'Obtenção de resultados e mensuração destes.'],
                ['Gestão de projeto - Como eu me vejo?', 'Capacidade de aplicar conhecimento, planejar, alocar e coordenar recursos, pessoas e atividades.'],
                ['Gestão de projeto - Como meu gestor me vê?', 'Capacidade de aplicar conhecimento, planejar, alocar e coordenar recursos, pessoas e atividades.'],
            ],
        );

        $this->criarFormulario(
            $empresa,
            FormularioTipo::Industria,
            'Avaliação de Desempenho - Indústria',
            'Modelo para Indústria. Aplicado nos ciclos de 90 dias, 6 meses e 1 ano.',
            [
                ['Presença no trabalho', 'O colaborador precisa organizar a vida pessoal para garantir a sua frequência constante. Considere o histórico de presença.'],
                ['Pontualidade', 'Cumprimento dos horários de entrada, intervalos e saída de acordo com a orientação da liderança do setor. Considere o histórico dos horários.'],
                ['Interesse', 'Assimilação no aprendizado das rotinas, conhecimento dos resultados, prontidão para contribuir, disponibilidade e flexibilidade de horários.'],
                ['Respeito às normas', 'Cumprimento das normas internas, orientações da liderança, padrão de qualidade, custo e segurança, uso de uniformes e EPIs, comunicação de saídas, organização e limpeza.'],
                ['Respeito interpessoal', 'Comportamento sem conflitos pessoais, maduro e alinhado com as orientações pontuais do dia a dia.'],
                ['Treinamento operacional', 'Qual a percepção que o colaborador tem sobre o acompanhamento feito pela liderança?'],
            ],
        );
    }

    private function criarFormulario(Empresa $empresa, FormularioTipo $tipo, string $nome, string $descricao, array $perguntas): void
    {
        $formulario = Formulario::updateOrCreate(
            ['empresa_id' => $empresa->id, 'tipo' => $tipo->value],
            [
                'nome' => $nome,
                'descricao' => $descricao,
                'versao' => 1,
                'is_active' => true,
            ],
        );

        foreach ($perguntas as $index => [$titulo, $descricao]) {
            Pergunta::updateOrCreate(
                ['formulario_id' => $formulario->id, 'ordem' => $index + 1],
                [
                    'titulo' => $titulo,
                    'descricao' => $descricao,
                    'tipo' => PerguntaTipo::TextoLongo,
                    'obrigatoria' => true,
                    'is_active' => true,
                ],
            );
        }
    }
}
