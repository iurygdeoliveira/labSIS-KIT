# Sistema Nativo de Changelog (Atualizações) — labSIS-KIT

O **labSIS-KIT** possui um sistema **100% nativo** para gerenciamento e exibição do histórico de atualizações da plataforma, eliminando dependências de plugins externos de terceiros.

O sistema segue o formato padronizado pelo [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/) e adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

---

## 1. Visão Geral e Funcionalidades

- **Autonomia Total:** Nenhuma dependência externa necessária para gerenciar o histórico.
- **Leitura Otimizada e Rolagem Infinita:** A página pública de visualização de atualizações (`ChangelogPage`) carrega as versões em lotes (8 por vez) via *infinite scroll*, garantindo altíssimo desempenho mesmo para históricos de longo prazo com milhares de registros.
- **Badge Informativo na Barra Lateral:** Sempre que uma nova atualização for lançada no sistema e ainda não tiver sido lida pelo usuário autenticado, um badge visual com a label **Novo** (na cor `info` / azul) será exibido na barra lateral ao lado do item de navegação **Atualizações**.
- **Controle de Leitura Inteligente:** Assim que o usuário acessa a página de Atualizações, o sistema atualiza automaticamente o registro de leitura (`last_read_changelog_at` na tabela de usuários), fazendo com que o badge de notificação desapareça da navegação.
- **Sincronização com o GitHub e Arquivo Local:** Possibilidade de importar ou sincronizar mudanças automaticamente a partir do arquivo `CHANGELOG.md` raiz ou diretamente de repositórios remotos do GitHub através do comando Artisan `php artisan changelog:sync-github`.

---

## 2. Controle de Acesso e Permissões (Roles)

O sistema foi estruturado rigorosamente com segregação de papéis:

1. **Super Administrador / Administrador (`admin` ou Role Admin):**
   - Possui acesso irrestrito de **CRUD (Criar, Ler, Atualizar, Editar e Deletar)** ao histórico de atualizações através do recurso administrativo `ChangelogResource` (**Gerenciar Changelog**).
   - Pode disparar a sincronização manual pelo botão **Sincronizar do GitHub** presente no topo da tabela de gerenciamento.
   - Pode visualizar a página pública **Atualizações**.

2. **Usuários de Organizações (Owner, Admin da Org ou Usuários Comuns):**
   - Têm acesso exclusivo de **Leitura (Somente Visualização)** na página pública de navegação **Atualizações** (`ChangelogPage`).
   - Jamais terão acesso, visibilidade na barra lateral ou permissão para gerenciar, alterar, criar ou excluir entradas do changelog.

---

## 3. Arquivos Principais da Arquitetura

A estrutura do módulo está organizada de forma coesa na arquitetura do Laravel/Filament:

### Models e Migrations
- `database/migrations/xxxx_xx_xx_xxxxxx_create_changelogs_table.php`: Tabela `changelogs` contendo os campos de uuid, versão, tipo, descrição, ordem, status de lançamento (`is_released`) e data de publicação (`released_at`).
- `database/migrations/0001_01_01_000000_create_users_table.php`: Tabela `users` consolidada contendo os campos de autenticação, perfil, `role` e a coluna `last_read_changelog_at` para rastrear a leitura de atualizações por usuário.
- `app/Models/Changelog.php`: Model Eloquent principal de manipulação dos registros de changelog.
- `app/Models/User.php`: Incorpora o helper method `hasUnreadChangelog()` e `markChangelogAsRead()`.

### Enums e Lógica de Parser
- `app/Enums/Changelog/ChangeType.php`: Enumeração dos tipos de mudança padrão *Keep a Changelog* (`Added`, `Changed`, `Deprecated`, `Removed`, `Fixed`, `Security`) com suporte a tradução para Português do Brasil, ícones e cores do Filament.
- `app/Support/Changelog/KeepAChangelogParser.php`: Parser Markdown robusto que interpreta o formato *Keep a Changelog* em inglês e português, extraindo datas, horários e autorias de commits.
- `app/Support/Changelog/VersionGrouper.php`: Agrupa e ordena as listas de modificações por versão e por tipo de mudança para apresentação visual nos cards.

### Filament Resources e Pages
- `app/Filament/Resources/Changelog/ChangelogResource.php`: Recurso administrativo completo do Filament para gerenciamento CRUD (tabela e formulário organizados modularmente).
- `app/Filament/Resources/Changelog/Pages/*`: Páginas `ListChangelogs`, `CreateChangelog`, `EditChangelog`, `ViewChangelog` e `DeleteChangelog`.
- `app/Filament/Pages/ChangelogPage.php`: Página de visualização pública das atualizações com sistema de filtro por versão, busca por texto em tempo real e rolagem infinita (*Infinite Scroll*).
- `app/Support/Changelog/ChangelogPlugin.php`: Plugin nativo que encapsula a lógica e faz o registro contínuo nos provedores do Filament.

### Provedores (Providers)
- `app/Providers/Filament/AdminPanelProvider.php`: Registra o `ChangelogPlugin`, habilitando gestão total no painel de administração.
- `app/Providers/Filament/UserPanelProvider.php`: Registra o `ChangelogPlugin`, habilitando visualização das atualizações na barra lateral dos usuários de organizações.

---

## 4. Como Usar e Sincronizar via Linha de Comando (CLI)

Para popular ou sincronizar o banco de dados com as notas de lançamento escritas no arquivo `CHANGELOG.md`:

```bash
# Sincronização padrão (atualiza ou insere novos registros sem excluir os anteriores)
php artisan changelog:sync-github

# Sincronização limpa (exclui os registros existentes da tabela e recarrega tudo do CHANGELOG.md)
php artisan changelog:sync-github --fresh
```

---

## 5. Exemplo de Registro no `CHANGELOG.md`

Para que o parser leia corretamente as modificações no arquivo `CHANGELOG.md` na raiz do projeto, utilize a seguinte sintaxe:

```markdown
## [1.2.0] - 2026-07-27

### Adicionado
- Implementação nativa e autônoma do sistema de Changelog no labSIS-KIT (por Iury em 27/07/2026 14:00).
- Adicionado badge visual informativo (info) ao lado do item de navegação Atualizações.

### Modificado
- Otimização das consultas de banco de dados no painel de usuários.
```
