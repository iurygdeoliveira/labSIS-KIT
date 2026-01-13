# LabSIS-KIT - Visão Geral do Projeto

## 🎯 Propósito
SaaS Starter Kit robusto e modular, construído para escalar. Focado em **multi-tenancy**, **gestão de mídia** e **segurança empresarial**.

## 🏗️ Stack Tecnológica (Real)
- **PHP**: 8.5.1
- **Laravel**: 12.46 (Bleeding Edge)
- **Filament**: 4.5
- **Banco de Dados**: PostgreSQL
- **Frontend**: Livewire 3.7 + Flux UI + Tailwind 4
- **Qualidade**: Pest 4.3 (Tests), Larastan 3.8 (Static Analysis)
- **Infra**: Laravel Sail (Docker)

## 📦 Pacotes Principais
- `spatie/laravel-permission`: RBAC e Permissions
- `spatie/laravel-medialibrary`: Gestão de arquivos
- `rappasoft/laravel-authentication-log`: Auditoria de login
- `livewire/flux`: Componentes de UI modernos

## 🔑 Módulos Core
1. **Tenancy**: Isolamento lógico via `App\Models\Tenant` e `team_id`.
2. **Auth**: Sanctum + Filament Auth + Log de Auditoria.
3. **Media**: `MediaService` centralizado + Integração FFmpeg para vídeos.

## 📊 Estrutura de Diretórios Chave
- `app/Filament/Clusters`: Agrupamento de recursos (ex: Permissions).
- `app/Services`: Lógica de negócios isolada (ex: `MediaService`).
- `resources/css/filament`: Sistema de temas customizado.

---
*Gerado com dados reais via Laravel Boost e Serena.*
