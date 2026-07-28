# Histórico de Alterações (Changelog)

Todas as alterações notáveis no projeto **labSIS-KIT** serão documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [1.2.0] - 2026-07-27

### Adicionado
- Implementação nativa e autônoma do sistema de **Changelog** no labSIS-KIT, substituindo dependências de pacotes de terceiros (por Iury em 27/07/2026 14:00).
- Adicionado badge visual informativo (`info`) ao lado do item de navegação **Atualizações** na barra lateral, alertando o usuário sobre novidades ainda não lidas (por Iury em 27/07/2026 14:05).
- Página de visualização pública das atualizações (`/admin/changelog`) com suporte a rolagem infinita para garantir alta performance mesmo em históricos longos (por Iury em 27/07/2026 14:10).
- Recurso administrativo exclusivo para usuários **Super Administrador** e **Administrador** gerenciarem registros no sistema (por Iury em 27/07/2026 14:15).
- Comando Artisan `changelog:sync-github` que permite obter e sincronizar mudanças automaticamente a partir de repositórios do GitHub ou arquivos locais (por Iury em 27/07/2026 14:20).
- Comando Artisan `changelog:reset-unread` com suporte a auto-sincronização automática e limpeza total de cache/notificações para reset em lote (por Iury em 27/07/2026 15:50).

### Modificado
- Redesign do **NotificationCenter** no Filament com cabeçalho personalizado contendo botões no topo (`Marcar tudo como lido` em tom *success* e `Limpar` em *danger*), sem abas superiores de categorias (por Iury em 27/07/2026 15:30).
- Ajuste de encaixe ponta a ponta e estilização da borda lateral do card de notificação no tom `info` (azul), com timestamps em formato humano (*há X minutos*) e prevenção total de rolagem horizontal no slide-over (por Iury em 27/07/2026 16:15).
- Desacoplamento da autorização do `ChangelogResource` para uma Laravel Policy dedicada (`ChangelogPolicy.php`) registrada no `AppServiceProvider`, em conformidade com as diretrizes do SDD Tasks (por Iury em 27/07/2026 14:45).
- Otimização de consultas N+1 nas queries de notificações do `NotificationCenter` e verificações no model `User` (por Iury em 27/07/2026 13:40).
- Consolidação das colunas `role` e `last_read_changelog_at` na migração base de usuários (por Iury em 27/07/2026 14:30).

## [1.1.0] - 2026-07-14

### Adicionado
- Módulo de gerenciamento de sessões de usuário com monitoramento e revogação de acessos ativos no painel de segurança.
- Suporte a verificação em duas etapas (MFA) via aplicativos autenticadores (Google Authenticator, Authy).

### Modificado
- Otimização das consultas de banco de dados no carregamento de organizações e permissões no painel de usuários.

## [1.0.0] - 2026-07-01

### Adicionado
- Lançamento inicial da plataforma **labSIS-KIT**.
- Estrutura completa multi-tenant baseada em Organizações e convites de membros.
- Integração de permissões granulares e controle de papéis (Owner, Admin, User).
- Painéis distintos de Administração Global (`admin`) e Painel do Usuário (`user`) utilizando Filament PHP.
