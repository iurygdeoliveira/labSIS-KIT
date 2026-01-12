<?php

declare(strict_types=1);

use App\Enums\RoleType;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('benchmark spa vs mpa navigation performance', function () {
    /** @var \Tests\TestCase $this */
    // Cria roles necessárias (o teste usa RefreshDatabase)
    Role::create(['name' => RoleType::ADMIN->value, 'guard_name' => 'web']);
    Role::create(['name' => RoleType::USER->value, 'guard_name' => 'web']);

    // Cria usuário admin com email verificado
    $user = User::factory()
        ->admin()
        ->create([
            'email' => 'benchmark@example.com',
        ]);

    // Configuração: 5 rodadas para confiabilidade estatística
    $iterations = 5;
    $results = [];

    // Detecta se SPA está ativado
    $providerContent = file_get_contents(app_path('Providers/Filament/BasePanelProvider.php'));
    $spaEnabled = ! str_contains($providerContent, '// ->spa()') && str_contains($providerContent, '->spa()');
    $mode = $spaEnabled ? 'SPA_ENABLED' : 'SPA_DISABLED';

    echo "\n\n";
    echo "================================================\n";
    echo "INICIANDO BENCHMARK HTTP - MODO: {$mode}\n";
    echo "================================================\n";
    echo "Rodadas: {$iterations}\n";
    echo "Navegações por rodada: 3 (Dashboard → Users → Tenants → Dashboard)\n";
    echo "================================================\n\n";

    // Autentica o usuário para todas as requisições
    $this->actingAs($user);

    // Executa múltiplas rodadas
    for ($i = 1; $i <= $iterations; $i++) {
        echo "Rodada {$i}/{$iterations}... ";

        $navigationMetrics = [
            'times' => [],
            'bytes' => [],
        ];

        // Navegação 1: Dashboard → Users
        $start = microtime(true);
        $response = $this->get('/admin/users');
        $duration = (microtime(true) - $start) * 1000; // Converte para ms

        $response->assertSuccessful();
        $navigationMetrics['times'][] = $duration;
        $navigationMetrics['bytes'][] = strlen($response->getContent());

        // Navegação 2: Users → Tenants
        $start = microtime(true);
        $response = $this->get('/admin/tenants');
        $duration = (microtime(true) - $start) * 1000;

        $response->assertSuccessful();
        $navigationMetrics['times'][] = $duration;
        $navigationMetrics['bytes'][] = strlen($response->getContent());

        // Navegação 3: Tenants → Dashboard
        $start = microtime(true);
        $response = $this->get('/admin');
        $duration = (microtime(true) - $start) * 1000;

        $response->assertSuccessful();
        $navigationMetrics['times'][] = $duration;
        $navigationMetrics['bytes'][] = strlen($response->getContent());

        // Calcula médias desta rodada
        $results[] = [
            'avg_time' => array_sum($navigationMetrics['times']) / 3,
            'avg_bytes' => array_sum($navigationMetrics['bytes']) / 3,
        ];

        echo "✓\n";
    }

    // Estatísticas finais
    $avgTime = array_sum(array_column($results, 'avg_time')) / $iterations;
    $avgBytes = array_sum(array_column($results, 'avg_bytes')) / $iterations;

    // Desvio padrão do tempo
    $variance = 0;
    foreach ($results as $result) {
        $variance += pow($result['avg_time'] - $avgTime, 2);
    }
    $stdDev = sqrt($variance / $iterations);

    // Formata relatório
    $timestamp = now()->format('Y-m-d H:i:s');

    $avgTimeFormatted = round($avgTime, 0).'ms';
    $stdDevFormatted = round($stdDev, 0).'ms';
    $avgBytesFormatted = formatBytes((int) $avgBytes);

    $report = <<<REPORT

    ================================================
    BENCHMARK RESULTS (HTTP) - {$timestamp}
    ================================================

    Configuration:
    - Mode: {$mode}
    - Panel: admin
    - Method: HTTP Requests (Backend Performance)
    - Iterations: {$iterations}
    - Navigations per iteration: 3

    ================================================
    RESULTS:
    ================================================
    Tempo Médio de Navegação:    {$avgTimeFormatted} (±{$stdDevFormatted})
    Payload Médio HTML:           {$avgBytesFormatted}
    ================================================

    Detalhes por Rodada:

    REPORT;

    foreach ($results as $index => $result) {
        $rodada = $index + 1;
        $timeFormatted = round($result['avg_time'], 0).'ms';
        $bytesFormatted = formatBytes((int) $result['avg_bytes']);

        $report .= "  [{$rodada}] Tempo: {$timeFormatted} | ";
        $report .= "Payload: {$bytesFormatted}\n";
    }

    $report .= "\n================================================\n";
    $report .= "\nNOTA: Teste HTTP mede performance backend.\n";
    $report .= "Não inclui renderização JavaScript/CSS do navegador.\n";
    $report .= "Para métricas completas de UX, considere usar Lighthouse CI.\n";
    $report .= "================================================\n";

    // Salva em arquivo
    $logFile = storage_path("logs/benchmark_http_{$mode}.log");
    file_put_contents($logFile, $report);

    // Exibe no console
    echo $report;
    echo "\nRelatório salvo em: {$logFile}\n\n";

    // Expectativas de performance (HTTP é mais rápido que browser)
    if ($spaEnabled) {
        // Em modo SPA, o backend deve responder rápido
        expect($avgTime)->toBeLessThan(500, 'Backend SPA deve responder < 500ms');
        // Payload pode ser similar (HTML completo vs JSON depende da implementação)
        expect($avgBytes)->toBeLessThan(500 * 1024, 'Payload deve ser < 500KB');
    } else {
        // Em modo MPA, verifica se não está absurdamente lento
        expect($avgTime)->toBeLessThan(1000, 'Backend MPA não deve ultrapassar 1s');
    }
});
