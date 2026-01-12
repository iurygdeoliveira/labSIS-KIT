# Metodologia de Teste de Performance: SPA vs MPA

## Objetivo

Comparar objetivamente o desempenho entre o modo **Single Page Application (SPA)** (padrão do Filament v3/v4) e o modo tradicional **Multi-Page Application (MPA)** através de métricas técnicas e percepção do usuário.

---

## Critérios de Avaliação

### 1️⃣ Tempo de Navegação (Navigation Time)

**Definição:** Intervalo entre o clique em um link de navegação e a renderização completa da nova página.

**Justificativa:**

-   **SPA:** Atualiza apenas o `<main>` via Livewire sem recarregar CSS/JS. Ganho esperado: **60-80% mais rápido**.
-   **MPA:** Força `document.load` completo, reprocessando todo o DOM e assets.
-   **Impacto no usuário:** Navegação fluida vs. "flash branco" entre páginas.

**Método de medição:**

-   Usa `performance.now()` do navegador antes/depois de cada transição
-   Média de **5 navegações** para eliminar variações de rede/GC

**Valores esperados:**

-   SPA: `200-400ms`
-   MPA: `800-1500ms`

---

### 2️⃣ Payload de Rede (Network Transfer Size)

**Definição:** Total de bytes transferidos durante a navegação.

**Justificativa:**

-   **SPA:** Apenas JSON do Livewire (~5-20KB)
-   **MPA:** HTML completo + re-validação de assets (~150-350KB)
-   **Impacto:** Economia de **90%+ de banda** crítica para conexões móveis 4G/5G

**Método de medição:**

-   `Performance Resource Timing API` captura `transferSize` de cada request
-   Soma total de bytes transferidos na transição

**Valores esperados:**

-   SPA: `8-25KB`
-   MPA: `180-400KB`

---

### 3️⃣ Número de Requisições HTTP

**Definição:** Quantidade de requests HTTP durante a navegação.

**Justificativa:**

-   **SPA:** 1-2 requests (Livewire update)
-   **MPA:** 15-30 requests (HTML + assets re-validados)
-   **Impacto:** Menos requests = menor latência cumulativa (cada round-trip adiciona ~40-100ms)

**Método de medição:**

-   Conta entradas em `performance.getEntriesByType('resource')`

**Valores esperados:**

-   SPA: `1-3 requests`
-   MPA: `18-35 requests`

---

### 4️⃣ Largest Contentful Paint (LCP)

**Definição:** Tempo até o maior elemento visível ser renderizado (Web Vital oficial do Google).

**Justificativa:**

-   Métrica core para **SEO** e **experiência do usuário**
-   SPA pode ter **LCP melhor em navegações**, mas pior na carga inicial
-   Threshold: `<2.5s` (bom), `2.5-4s` (médio), `>4s` (ruim)

**Método de medição:**

-   `PerformanceObserver` com `entryType: 'largest-contentful-paint'`

**Valores esperados (navegação):**

-   SPA: `250-500ms`
-   MPA: `600-1200ms`

---

### 5️⃣ Confiabilidade Estatística (Múltiplas Execuções)

**Definição:** Execução de **5 rodadas completas** calculando média e desvio padrão.

**Justificativa:**

-   **Variância de rede:** Latência pode oscilar ±100ms entre requests
-   **Garbage Collection:** JavaScript pode pausar execução aleatoriamente
-   **Cache:** Primeira execução sempre mais lenta (DNS, TLS handshake)
-   Descarta outliers (±2σ) para resultados confiáveis

**Método de medição:**

-   Loop de 5 iterações com mesma sequência de navegação
-   Cálculo de média aritmética e desvio padrão

---

## Como Executar o Teste

### Pré-requisitos

1. Ambiente Laravel Sail rodando
2. Dados de seed básicos (usuários, tenants)
3. Painéis Admin e User acessíveis

### Execução Automatizada

O teste está em `tests/Browser/Performance/SpaBenchmarkTest.php` e executa **automaticamente**:

#### Passo 1: Benchmark com SPA ATIVADO

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

#### Passo 2: Benchmark com SPA DESATIVADO

Edite `app/Providers/Filament/BasePanelProvider.php`:

```php
// ->spa()  // Comente esta linha
```

Execute novamente:

```bash
./vendor/bin/sail artisan test tests/Browser/Performance/SpaBenchmarkTest.php
```

O relatório será salvo em `storage/logs/benchmark_spa_disabled.log`.

#### Passo 3: Comparação dos Resultados

Compare os dois arquivos de log ou veja o resumo no console:

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

---

## Interpretação dos Resultados

### ✅ Critérios para MANTER SPA ativado:

-   Navegação **≥40% mais rápida** que MPA
-   Payload de rede **≥70% menor**
-   LCP **≥30% melhor**
-   Aplicação com **navegação frequente** entre páginas

### ⚠️ Critérios para CONSIDERAR DESATIVAR SPA:

-   Diferença **<15%** no tempo de navegação (pode indicar gargalo no backend)
-   Usuários predominantemente em **dispositivos muito antigos** (overhead de JS)
-   Aplicação com **páginas isoladas** (pouca navegação interna)

### 🔍 Investigação Necessária se:

-   **SPA não mostra ganho significativo:**

    -   Backend pode estar lento (verificar query times no Telescope)
    -   Assets muito pesados (rodar `npm run build` e verificar bundle size)
    -   Livewire mal configurado (verificar `livewire.php`)

-   **MPA surpreendentemente rápido:**
    -   Cache HTTP agressivo pode estar mascarando recargas
    -   Servidor com GZIP/Brotli muito eficiente
    -   Navegador com cache local forte

---

## Limitações do Teste

1. **Não testa em dispositivos reais:** Usa Chrome headless (simula desktop)
2. **Rede local:** Latência artificial pode não refletir 4G real
3. **Sem múltiplos browsers:** Testa apenas Chrome (Firefox/Safari podem ter resultados diferentes)
4. **Dados sintéticos:** Quantidade de registros pode impactar query times

---

## Próximos Passos

Após análise dos resultados:

1. **Se SPA vencer claramente:** Manter ativado e documentar ganhos
2. **Se empatar:** Testar em produção com usuários reais (A/B test)
3. **Se MPA vencer:** Investigar gargalos de JS antes de desativar SPA
