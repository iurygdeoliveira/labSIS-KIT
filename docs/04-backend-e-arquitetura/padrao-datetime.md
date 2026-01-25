# Padronização de Data e Hora

Esta seção descreve as práticas recomendadas para manipulação de dados temporais no backend da aplicação, visando consistência e prevenção de bugs relacionados à mutabilidade.

## Objetivo

O objetivo principal é garantir que todas as operações de data e hora sejam **imutáveis**. A manipulação de objetos de data mutáveis (`Carbon` padrão ou `DateTime`) pode levar a efeitos colaterais indesejados onde a alteração de uma data em um ponto do código afeta inadvertidamente outras partes que referenciam o mesmo objeto.

Além disso, centralizamos a lógica de datas em um wrapper da aplicação, facilitando a manutenção e a adição de métodos auxiliares personalizados no futuro.

## Implementação

### 1. Wrapper `AppDateTime`

Criamos a classe `App\Support\AppDateTime` que estende `Carbon\CarbonImmutable`.

```php
namespace App\Support;

use Carbon\CarbonImmutable;

class AppDateTime extends CarbonImmutable
{
    // Wrapper para garantir imutabilidade e permitir métodos customizados
}
```

### 2. Uso em Recursos Filament

Substituímos o uso direto de `Carbon::parse()` por `AppDateTime::parse()` em Infolists e Tables.

**Exemplo em Infolist (TenantInfolist.php):**

```php
use App\Support\AppDateTime;

// ...

TextEntry::make('created_at')
    ->label('Criado em')
    ->formatStateUsing(fn ($state) => $state ? AppDateTime::parse($state)->format('d/m/Y H:i') : null),
```

**Exemplo em Table (MediaTable.php):**

```php
use App\Support\AppDateTime;

// ...

TextColumn::make('created_at_display')
    ->state(function ($record) {
        // ...
        return $createdAt ? AppDateTime::parse($createdAt)->format('d/m/Y H:i') : '—';
    }),
```

## Resultados Esperados

-   **Redução de Bugs:** Eliminação de erros causados por modificação acidental de objetos de data compartilhados.
-   **Consistência:** Todo o código novo deve utilizar `AppDateTime` em vez de helpers genéricos ou classes nativas mutáveis.
-   **Manutenibilidade:** Ponto central para correções ou alterações no comportamento de datas da aplicação.

## Referências

- [Support: AppDateTime](/app/Support/AppDateTime.php)
