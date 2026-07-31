<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Changelog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ChangelogSeeder extends Seeder
{
    /**
     * Seed the application's changelog database table with user-friendly release descriptions and coherent pre-release versioning (v0.x).
     */
    public function run(): void
    {
        $changelogs = [
            [
                'version' => 'v0.17.0',
                'released_at' => '2026-07-31',
                'description' => 'Ajuste na posição do contador de notificações (o número em cima do sininho), movendo-o para o canto superior esquerdo para que a barra superior fique mais alinhada e bonita.',
                'sort' => 0,
            ],
            [
                'version' => 'v0.16.0',
                'released_at' => '2026-07-28',
                'description' => 'Simplificação da tela de histórico de atualizações (Changelog). Agora a navegação e a leitura das novidades do sistema estão mais diretas e fáceis de usar.',
                'sort' => 1,
            ],
            [
                'version' => 'v0.15.0',
                'released_at' => '2026-07-27',
                'description' => 'Novo design para a central de notificações e adição da funcionalidade de gerenciar o registro de alterações (Changelog), permitindo que os usuários recebam avisos e leiam as novidades do sistema.',
                'sort' => 2,
            ],
            [
                'version' => 'v0.14.0',
                'released_at' => '2026-07-26',
                'description' => 'Melhoria de performance (otimização de consultas repetidas ao banco de dados) no centro de notificações e limpeza de códigos e pacotes antigos de segurança que não eram mais utilizados.',
                'sort' => 3,
            ],
            [
                'version' => 'v0.13.0',
                'released_at' => '2026-07-26',
                'description' => 'Refatoração na parte de controle de acesso (Tenant). Substituímos o tipo de perfil antigo por papéis organizacionais (OrganizationRole) e melhoramos a forma de gerenciar as Organizações (Tenants) no sistema.',
                'sort' => 4,
            ],
            [
                'version' => 'v0.12.0',
                'released_at' => '2026-07-26',
                'description' => 'Instalação e configuração inicial do recurso de notificações, preparando a estrutura para que o sistema possa enviar avisos internos aos usuários.',
                'sort' => 5,
            ],
            [
                'version' => 'v0.11.0',
                'released_at' => '2026-07-19',
                'description' => 'Melhoria na navegação lateral do sistema: os grupos de menus agora são dinâmicos e a área de configurações foi renomeada para "Configurações da Organização" para deixar seu propósito mais claro.',
                'sort' => 6,
            ],
            [
                'version' => 'v0.10.0',
                'released_at' => '2026-05-27',
                'description' => 'Correção visual: Os botões principais nas páginas mais simples foram ajustados para exibir a cor verde escuro corretamente quando o sistema estiver no Modo Escuro (Dark Mode).',
                'sort' => 7,
            ],
            [
                'version' => 'v0.9.0',
                'released_at' => '2026-05-27',
                'description' => 'Correção no registro (logs) de quem faz login no sistema e pequenos ajustes no tema visual da área administrativa.',
                'sort' => 8,
            ],
            [
                'version' => 'v0.8.0',
                'released_at' => '2026-05-27',
                'description' => 'Ajuste de interface e layout visual no módulo de gerenciamento de equipes.',
                'sort' => 9,
            ],
            [
                'version' => 'v0.7.0',
                'released_at' => '2026-05-27',
                'description' => 'Implementação de cache de estatísticas (armazenamento rápido temporário) para melhorar consideravelmente o desempenho do painel principal.',
                'sort' => 10,
            ],
            [
                'version' => 'v0.6.0',
                'released_at' => '2026-05-27',
                'description' => 'Criação de um manual em HTML para o sistema, com barra lateral colapsável para facilitar a leitura, o aprendizado das regras de negócio e a documentação geral.',
                'sort' => 11,
            ],
            [
                'version' => 'v0.5.0',
                'released_at' => '2026-05-25',
                'description' => 'Melhoria no isolamento de dados: Migramos o controle de organizações para um novo sistema e integramos verificações de segurança, ajustando menus e o comportamento do painel.',
                'sort' => 12,
            ],
            [
                'version' => 'v0.4.0',
                'released_at' => '2026-05-06',
                'description' => 'Atualização do motor do sistema para a versão mais recente do framework (Laravel 13), inclusão de uma nova página inicial pública (landing page) e ajustes gerais internos no painel.',
                'sort' => 13,
            ],
            [
                'version' => 'v0.3.0',
                'released_at' => '2026-02-09',
                'description' => 'Correção de um erro técnico na listagem de usuários do sistema e restauração das proteções de acesso (middlewares) para reforçar a segurança no painel.',
                'sort' => 14,
            ],
            [
                'version' => 'v0.2.0',
                'released_at' => '2026-01-30',
                'description' => 'Adição de uma verificação automática (health check) para checar se o sistema de armazenamento de arquivos e imagens está funcionando corretamente durante a instalação.',
                'sort' => 15,
            ],
            [
                'version' => 'v0.1.0',
                'released_at' => '2026-01-29',
                'description' => 'Criação e organização da documentação do sistema, estabelecendo o planejamento futuro (Roadmap), detalhando regras do negócio, criando laboratórios educativos e definindo habilidades das ferramentas de Inteligência Artificial para o desenvolvimento contínuo.',
                'sort' => 16,
            ],
        ];

        // Se houver registros antigos com hashes de commit, limpa a tabela para aplicar o novo padrão v0.x
        Changelog::truncate();

        foreach ($changelogs as $item) {
            Changelog::create([
                'version' => $item['version'],
                'released_at' => $item['released_at'],
                'description' => $item['description'],
                'sort' => $item['sort'],
            ]);
        }

        Cache::forget('changelog:latest_date');
    }
}
