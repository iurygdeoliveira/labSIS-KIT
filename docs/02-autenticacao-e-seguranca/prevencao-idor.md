# Prevenção Contra IDOR (Insecure Direct Object Reference)

Esta seção documenta a estratégia de segurança adotada no projeto para mitigar vulnerabilidades de Referência Direta a Objetos Insegura (IDOR).

## O que é IDOR?

O **IDOR (Insecure Direct Object Reference)** ocorre quando uma aplicação expõe uma referência direta a um objeto de implementação interna, como um arquivo ou chave de banco de dados (ID sequencial), sem um mecanismo de controle de acesso ou verificação de autorização adequada.

Isso permite que atacantes manipulem essas referências (por exemplo, alterando um ID na URL de `/api/users/123` para `/api/users/124`) para acessar dados não autorizados. Se a aplicação confiar apenas na entrada do usuário para recuperar o objeto, o atacante pode acessar, modificar ou excluir dados de outros usuários.

## Estratégia de Mitigação no Projeto

Para blindar o sistema contra IDOR, adotamos uma abordagem em camadas:

1.  **Ofuscação de IDs (UUIDs)**: Substituição de chaves primárias incrementais (ex: `1`, `2`, `3`) por Identificadores Únicos Universais (UUIDs) aleatórios (ex: `a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11`). Isso torna a enumeração de recursos matematicamente inviável.
2.  **Autorização Robusta (Policies)**: Verificação estrita de propriedade e permissões em todas as camadas de acesso (Policies do Laravel), garantindo que, mesmo se um UUID for descoberto, o acesso seja negado se o usuário não tiver permissão sobre aquele recurso específico ou Tenant.
3.  **Escopo de Tenant**: Garantia de que todas as consultas são filtradas pelo Tenant ativo do usuário.

## Implementação Técnica

### 1. Adoção de UUIDs

Utilizamos um `UuidTrait` customizado que gera automaticamente um UUID v4 ao criar um novo registro e define o UUID como a chave de rota padrão (`getRouteKeyName`).

**Exemplo no Modelo (`App\Models\MediaItem.php`):**

```php
namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
{
    use UuidTrait; // <--- Habilita geração automática e Route Binding via UUID

    protected $fillable = [
        'uuid',
        'name',
        // ...
    ];
}
```

Isso garante que rotas geradas pelo Filament ou controllers utilizem o UUID automaticamente: `https://app.example.com/admin/media/a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11/edit`.

### 2. Estrutura do Banco de Dados

As tabelas sensíveis foram migradas para incluir uma coluna UUID indexada e única.

**Exemplo de Migração (`database/migrations/...create_media_items_table.php`):**

```php
Schema::create('media_items', function (Blueprint $table) {
    $table->id(); // ID interno (ainda usado para FKs por performance)
    $table->uuid('uuid')->unique(); // ID público (usado nas URLs)
    $table->string('name');
    // ...
});
```

### 3. Policies e Controle de Acesso

Além da ofuscação, as Policies verificam se o usuário tem permissão para interagir com o recurso dentro do contexto do seu Tenant.

**Exemplo (`App\Policies\MediaItemPolicy.php`):**

```php
public function view(User $user, MediaItem $record): bool
{
    // Verifica se o usuário tem permissão E se o recurso pertence ao escopo acessível
    return $user->can(Permission::VIEW->for('media'));
}
```

O Filament aplica escopos globais de Tenant automaticamente (`TenantScope`), assegurando que `MediaItem::find($uuid)` só retorne resultados que pertençam ao Tenant do usuário atual.

## Resultados Esperados

1.  **Impossibilidade de Enumeração**: Um atacante não consegue "adivinhar" o próximo ID (ex: tentar ID 101 após ver o 100) para acessar dados de outro cliente.
2.  **Segurança por Design**: O sistema falha de forma segura (404 Not Found) se um UUID inválido ou pertencente a outro tenant for acessado.
3.  **Proteção de Dados Sensíveis**: As referências internas do banco de dados (IDs numéricos) permanecem ocultas do usuário final, sendo usadas apenas internamente para chaves estrangeiras (preservando performance de JOINs).
