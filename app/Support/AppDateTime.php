<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonImmutable;

class AppDateTime extends CarbonImmutable
{
    // Este wrapper garante que todas as operações de data sejam imutáveis por padrão.
    // Métodos de conveniência podem ser adicionados aqui no futuro.
}
