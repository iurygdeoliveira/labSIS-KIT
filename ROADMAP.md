vv# LabSIS KIT Roadmap

Este documento centraliza as próximas funcionalidades e otimizações planejadas para o **LabSIS KIT**. Estes são os passos que pretendemos seguir para tornar este Starter Kit ainda mais completo e robusto.

---

## 🚀 Próximos Passos (Recursos a serem implementados)

### 👥 Usuários e Autenticação

- [ ] **Impersonação de Usuários:** Permitir que administradores globais acessem o painel como se fossem um usuário específico para facilitar o suporte.

### 🏢 Multi-tenancy

- [ ] **Customização de Branding por Tenant:** Permitir que cada tenant defina seu próprio logotipo e cores primárias no painel `/user`.

### 📥 Importação e Exportação de Dados

- [ ] **Trait/Função de Importação CSV:** Criar uma trait reutilizável ou função helper para importar dados a partir de arquivos CSV, facilitando a carga em massa de registros no sistema. Deve incluir:
  - Validação de estrutura do arquivo
  - Mapeamento flexível de colunas
  - Tratamento de erros e relatório de inconsistências
  - Suporte a grandes volumes de dados (processamento em lotes)
  - Integração com Filament para interface de upload

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

### Metodologia de Teste de Performance (SPA vs MPA)

> [!IMPORTANT]
> Este estudo estabelece um padrão de medição para futuras otimizações de performance.

#### Objetivo

Comparar objetivamente o desempenho entre o modo **Single Page Application (SPA)** (padrão do Filament v3/v4) e o modo tradicional **Multi-Page Application (MPA)** através de métricas técnicas e percepção do usuário.

#### Critérios de Avaliação

##### 1️⃣ Tempo de Navegação (Navigation Time)

**Definição:** Intervalo entre o clique em um link de navegação e a renderização completa da nova página.

**Justificativa:**
- **SPA:** Atualiza apenas o `<main>` via Livewire sem recarregar CSS/JS. Ganho esperado: **60-80% mais rápido**.
- **MPA:** Força `document.load` completo, reprocessando todo o DOM e assets.
- **Impacto no usuário:** Navegação fluida vs. "flash branco" entre páginas.

**Método de medição:**
- Usa `performance.now()` do navegador antes/depois de cada transição
- Média de **5 navegações** para eliminar variações de rede/GC

**Valores esperados:**
- SPA: `200-400ms`
- MPA: `800-1500ms`

##### 2️⃣ Payload de Rede (Network Transfer Size)

**Definição:** Total de bytes transferidos durante a navegação.

**Justificativa:**
- **SPA:** Apenas JSON do Livewire (~5-20KB)
- **MPA:** HTML completo + re-validação de assets (~150-350KB)
- **Impacto:** Economia de **90%+ de banda** crítica para conexões móveis 4G/5G

**Método de medição:**
- `Performance Resource Timing API` captura `transferSize` de cada request
- Soma total de bytes transferidos na transição

**Valores esperados:**
- SPA: `8-25KB`
- MPA: `180-400KB`

##### 3️⃣ Número de Requisições HTTP

**Definição:** Quantidade de requests HTTP durante a navegação.

**Justificativa:**
- **SPA:** 1-2 requests (Livewire update)
- **MPA:** 15-30 requests (HTML + assets re-validados)
- **Impacto:** Menos requests = menor latência cumulativa (cada round-trip adiciona ~40-100ms)

**Método de medição:**
- Conta entradas em `performance.getEntriesByType('resource')`

**Valores esperados:**
- SPA: `1-3 requests`
- MPA: `18-35 requests`

##### 4️⃣ Largest Contentful Paint (LCP)

**Definição:** Tempo até o maior elemento visível ser renderizado (Web Vital oficial do Google).

**Justificativa:**
- Métrica core para **SEO** e **experiência do usuário**
- SPA pode ter **LCP melhor em navegações**, mas pior na carga inicial
- Threshold: `<2.5s` (bom), `2.5-4s` (médio), `>4s` (ruim)

**Método de medição:**
- `PerformanceObserver` com `entryType: 'largest-contentful-paint'`

**Valores esperados (navegação):**
- SPA: `250-500ms`
- MPA: `600-1200ms`

##### 5️⃣ Confiabilidade Estatística

**Definição:** Execução de **5 rodadas completas** calculando média e desvio padrão.

**Justificativa:**
- **Variância de rede:** Latência pode oscilar ±100ms entre requests
- **Garbage Collection:** JavaScript pode pausar execução aleatoriamente
- **Cache:** Primeira execução sempre mais lenta (DNS, TLS handshake)
- Descarta outliers (±2σ) para resultados confiáveis

#### Como Executar o Teste

**Pré-requisitos:**
1. Ambiente Laravel Sail rodando
2. Dados de seed básicos (usuários, tenants)
3. Painéis Admin e User acessíveis

**Passo 1: Benchmark com SPA ATIVADO**

Certifique-se que `BasePanelProvider.php` contém:
```php
->spa()
```

Execute:
```bash
./vendor/bin/sail artisan test tests/Browser/Performance/SpaBenchmarkTest.php
```

O teste irá:
1. Fazer login no painel Admin
2. Executar **5 rodadas** de navegação: Dashboard → Users → Tenants → Dashboard
3. Capturar métricas de cada transição
4. Salvar relatório em `storage/logs/benchmark_spa_enabled.log`

**Passo 2: Benchmark com SPA DESATIVADO**

Edite `app/Providers/Filament/BasePanelProvider.php`:
```php
// ->spa()  // Comente esta linha
```

Execute novamente:
```bash
./vendor/bin/sail artisan test tests/Browser/Performance/SpaBenchmarkTest.php
```

O relatório será salvo em `storage/logs/benchmark_spa_disabled.log`.

**Passo 3: Comparação dos Resultados**

```
================================================
BENCHMARK: SPA ENABLED
================================================
Tempo Médio de Navegação:    324ms (±45ms)
Payload Médio de Rede:        14.2KB
Total de Requisições:         2.4 (avg)
LCP Médio:                    358ms
================================================

Vs.

================================================
BENCHMARK: SPA DISABLED
================================================
Tempo Médio de Navegação:    1,142ms (±178ms)
Payload Médio de Rede:        287KB
Total de Requisições:         24.6 (avg)
LCP Médio:                    892ms
================================================

RESULTADO: SPA é 71.6% mais rápido
```

#### Interpretação dos Resultados

**✅ Critérios para MANTER SPA ativado:**
- Navegação **≥40% mais rápida** que MPA
- Payload de rede **≥70% menor**
- LCP **≥30% melhor**
- Aplicação com **navegação frequente** entre páginas

**⚠️ Critérios para CONSIDERAR DESATIVAR SPA:**
- Diferença **<15%** no tempo de navegação (pode indicar gargalo no backend)
- Usuários predominantemente em **dispositivos muito antigos** (overhead de JS)
- Aplicação com **páginas isoladas** (pouca navegação interna)

**🔍 Investigação Necessária se:**

*SPA não mostra ganho significativo:*
- Backend pode estar lento (verificar query times no Telescope)
- Assets muito pesados (rodar `npm run build` e verificar bundle size)
- Livewire mal configurado (verificar `livewire.php`)

*MPA surpreendentemente rápido:*
- Cache HTTP agressivo pode estar mascarando recargas
- Servidor com GZIP/Brotli muito eficiente
- Navegador com cache local forte

#### Limitações do Teste

1. **Não testa em dispositivos reais:** Usa Chrome headless (simula desktop)
2. **Rede local:** Latência artificial pode não refletir 4G real
3. **Sem múltiplos browsers:** Testa apenas Chrome (Firefox/Safari podem ter resultados diferentes)
4. **Dados sintéticos:** Quantidade de registros pode impactar query times

#### Próximos Passos

Após análise dos resultados:
1. **Se SPA vencer claramente:** Manter ativado e documentar ganhos
2. **Se empatar:** Testar em produção com usuários reais (A/B test)
3. **Se MPA vencer:** Investigar gargalos de JS antes de desativar SPA

---

## 🛠️ Como Contribuir

Se você tem interesse em ajudar no desenvolvimento de algum destes itens, sinta-se à vontade para abrir uma issue ou enviar um Pull Request.

---

<div align="center">
  <strong>LabSIS - Transformando desafios reais em soluções inteligentes</strong>
</div>
