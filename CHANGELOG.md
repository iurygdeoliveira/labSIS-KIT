# Histórico de Alterações (Changelog)

Todas as alterações notáveis no projeto **labSIS-KIT** serão documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [1.2.0] - 2026-07-27

### Adicionado
- Implementação nativa e autônoma do sistema de **Changelog** no labSIS-KIT, substituindo dependências de pacotes de terceiros (por Iury em 27/07/2026 14:00).
- Adicionado badge visual informativo (`info`) ao lado do item de navegação **Atualizações** na barra lateral, alertando o usuário sobre novidades ainda não lidas (por Iury em 27/07/2026 14:05).
- Página de visualização pública das atualizações com suporte a rolagem infinita para garantir alta performance mesmo em históricos longos (por Iury em 27/07/2026 14:10).
- Recurso administrativo exclusivo para usuários **Super Administrador** e **Administrador** criarem, alterarem, editarem e excluírem registros no sistema (por Iury em 27/07/2026 14:15).
- Comando Artisan `changelog:sync-github` que permite obter e sincronizar mudanças automaticamente a partir de repositórios do GitHub ou arquivos locais (por Iury em 27/07/2026 14:20).

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
