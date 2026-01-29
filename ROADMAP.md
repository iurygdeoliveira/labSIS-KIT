vv# LabSIS KIT Roadmap

Este documento centraliza as próximas funcionalidades e otimizações planejadas para o **LabSIS KIT**. Estes são os passos que pretendemos seguir para tornar este Starter Kit ainda mais completo e robusto.

---

## 🚀 Próximos Passos (Recursos a serem implementados)

### 👥 Usuários e Autenticação

- [ ] **Impersonação de Usuários:** Permitir que administradores globais acessem o painel como se fossem um usuário específico para facilitar o suporte.

### 🏢 Multi-tenancy

- [ ] **Customização de Branding por Tenant:** Permitir que cada tenant defina seu próprio logotipo e cores primárias no painel `/user`.

### ⚡ Performance e Monitoramento

- [ ] **Benchmarks Automatizados:** Integração contínua de testes de performance (SPA vs MPA) para garantir que as atualizações não degradem a experiência do usuário.
- [ ] **Logs via MongoDB:** Refatorar o sistema de logs de atividades para utilizar MongoDB como storage padrão, garantindo escalabilidade e performance em aplicações com alto tráfego.
- [ ] **Laravel Octane + FrankenPHP (PHP 8.5-ZTS):** Migrar para FrankenPHP com PHP-ZTS 8.5 para habilitar worker mode e melhorar performance de requisições. Implementação baseada em [PHP 8.5 com Laravel Octane e FrankenPHP - The Missing Manual](https://danielpetrica.com/running-php-8-5-with-laravel-octane-and-frankenphp-the-missing-manual/). Inclui:
  - Instalação de PHP-ZTS 8.5 via repositório Henderkes
  - Configuração de extensões ZTS (bcmath, gd, intl, mysql, mbstring, etc.)
  - Debug logging com `--log-level=debug` para troubleshooting
  - Arquitetura de alta performance com Traefik + FrankenPHP

---

## 📚 Conteúdo Educacional

- [ ] **Laboratório: Particionamento PostgreSQL** - Workshop prático de particionamento de tabelas grandes para fins educacionais.
  
  **Inspiração**: [Filament Slow on Large Table - Optimize with PostgreSQL Partitions](https://filamentmastery.com/articles/filament-slow-on-large-table-optimize-with-postgres-partitions)
  
  **Contexto Educacional**: Este laboratório demonstra particionamento PostgreSQL sem comprometer a arquitetura produtiva. A tabela `users` **não será particionada** porque a arquitetura multi-tenant com tabelas pivot (`tenant_user`, `model_has_roles`) já distribui carga eficientemente.
  
  **Tabela de Demonstração**: `notifications` (nativa do Laravel)
  - ✅ Cresce naturalmente com uso do sistema
  - ✅ Padrão de acesso temporal (queries filtram por data)
  - ✅ Política de retenção (descartar notificações antigas)
  - ✅ Consistente com arquitetura híbrida (PostgreSQL, não MongoDB)
  
  **Estrutura do Laboratório**:
  
  1. **Preparação (Aula 1 - 2h)**
     - Teoria: O que é particionamento? Tipos (Range, List, Hash)
     - Análise: Por que `users` não precisa ser particionada?
     - Prática: Criar tabela `notifications`, popular com 1M de registros via seeder
     - Benchmark inicial de queries
  
  2. **Implementação (Aula 2 - 2h)**
     - Migration de particionamento Range (trimestral)
     - Criar 9 partições (2024-2026)
     - Índices especializados por partição
     - Benchmark comparativo (com/sem partition pruning)
     - Análise com `EXPLAIN ANALYZE`
  
  3. **Automação (Aula 3 - Opcional)**
     - Comando Artisan para criar partições futuras
     - Política de retenção (descartar partições > 12 meses)
     - Agendamento via Laravel Scheduler
  
  **Exercícios Práticos**:
  
  ```php
  // Query 1: Com partition pruning (rápida)
  DB::table('notifications')
      ->whereNull('read_at')
      ->whereBetween('created_at', [now()->subMonths(3), now()])
      ->count();
  
  // Query 2: Sem partition pruning (lenta)
  DB::table('notifications')
      ->where('notifiable_type', 'App\\Models\\User')
      ->where('notifiable_id', 1)
      ->count();
  ```
  
  **Comparação Educacional**: Particionamento vs. Arquitetura Pivot
  
  | Aspecto | Particionamento PostgreSQL | Multi-Tenant Pivot (labSIS-KIT) |
  |:--------|:---------------------------|:--------------------------------|
  | **Quando Usar** | Milhões de registros + padrão temporal | Relacionamentos N:M complexos |
  | **Benefício** | Partition Pruning (queries filtradas por data) | Índices especializados + Cache eficiente |
  | **Complexidade** | 🔴 Alta (migrations, gerenciamento) | 🟢 Baixa (Eloquent nativo) |
  | **Caso Ideal** | Notificações, telemetria, analytics | Multi-tenancy, RBAC, marketplaces |
  
  **Entregável**: Relatório em Markdown com benchmarks, análise de `EXPLAIN ANALYZE` e discussão sobre trade-offs

---

## 📊 Pesquisas e Metodologias


- [ ] **Metodologia: SPA vs MPA** - Estudo detalhado sobre os ganhos de performance ao utilizar o modo Single Page Application do Filament.

---

## 🛠️ Como Contribuir

Se você tem interesse em ajudar no desenvolvimento de algum destes itens, sinta-se à vontade para abrir uma issue ou enviar um Pull Request.

---

<div align="center">
  <strong>LabSIS - Transformando desafios reais em soluções inteligentes</strong>
</div>
