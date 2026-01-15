# MongoDB Integration

## Visão Geral

Este projeto suporta **MongoDB** como banco de dados secundário, rodando paralelamente ao **PostgreSQL** (banco principal). A integração utiliza o pacote oficial `mongodb/laravel-mongodb` mantido pela MongoDB.

## Stack Técnica

-   **Banco de Dados**: MongoDB Atlas Local (via Docker)
-   **Driver PHP**: `php8.5-mongodb` (já instalado no Dockerfile)
-   **Pacote Laravel**: `mongodb/laravel-mongodb` (a ser instalado via Composer)
-   **Porta Padrão**: 27017

## Configuração

### 1. Variáveis de Ambiente

As seguintes variáveis já estão configuradas no `.env.example`:

```ini
MONGODB_URI=mongodb://sail:password@mongodb:27017
MONGODB_USERNAME=sail
MONGODB_PASSWORD=password
MONGODB_DATABASE=labsis
FORWARD_MONGODB_PORT=27017
```

**Copie estas variáveis para o seu `.env` local.**

### 2. Instalação do Pacote Laravel MongoDB

Instale o pacote oficial via Composer:

```bash
vendor/bin/sail composer require mongodb/laravel-mongodb
```

### 3. Iniciar os Containers

Reconstrua os containers para incluir o MongoDB:

```bash
vendor/bin/sail down
vendor/bin/sail up -d
```

### 4. Verificar Conexão

Teste a conexão com MongoDB:

```bash
vendor/bin/sail artisan tinker
```

No Tinker, execute:

```php
DB::connection('mongodb')->getMongoDB()->listCollections();
```

## Usando MongoDB

### Criar um Model MongoDB

Os Models do MongoDB estendem `MongoDB\Laravel\Eloquent\Model`:

```php
<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'logs';

    protected $fillable = [
        'message',
        'level',
        'context',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
```

### Usar em Queries

```php
// Salvar documento
Log::create([
    'message' => 'User logged in',
    'level' => 'info',
    'context' => ['user_id' => 123, 'ip' => '192.168.1.1'],
]);

// Buscar documentos
$logs = Log::where('level', 'error')->get();

// Usar operadores MongoDB
$recentLogs = Log::where('created_at', '>=', now()->subDays(7))->get();
```

### Hybrid Models (PostgreSQL + MongoDB)

Você pode usar relações entre PostgreSQL e MongoDB:

```php
// Model PostgreSQL
class User extends Authenticatable
{
    public function logs()
    {
        return $this->hasMany(Log::class, 'user_id', 'id');
    }
}

// Usar em queries
$user = User::find(1);
$userLogs = $user->logs;
```

## Casos de Uso Recomendados

✅ **Use MongoDB para**:

-   Logs de aplicação
-   Analytics e eventos
-   Cache de dados complexos
-   Dados não estruturados
-   Histórico de atividades
-   Notificações e mensagens

❌ **Continue usando PostgreSQL para**:

-   Dados relacionais (users, tenants, etc.)
-   Transações ACID
-   Estruturas fixas com migrations
-   Integridade referencial

## Comandos Úteis

### Acessar MongoDB Shell

```bash
vendor/bin/sail mongodb mongosh -u sail -p password --authenticationDatabase admin
```

### Ver Databases e Collections

```javascript
// No mongosh
show dbs
use labsis
show collections
```

### Backup MongoDB

```bash
vendor/bin/sail exec mongodb mongodump --uri="mongodb://sail:password@localhost:27017" --out=/data/backup
```

### Restaurar Backup

```bash
vendor/bin/sail exec mongodb mongorestore --uri="mongodb://sail:password@localhost:27017" /data/backup
```

## Health Check

O container MongoDB possui healthcheck configurado que verifica se o banco está respondendo:

```yaml
healthcheck:
    test:
        - CMD
        - mongosh
        - --eval
        - "db.adminCommand('ping')"
    retries: 3
    timeout: 5s
```

## Troubleshooting

### Erro: "Class 'MongoDB\Driver\Manager' not found"

A extensão PHP já está instalada. Verifique se os containers foram reconstruídos:

```bash
vendor/bin/sail down
vendor/bin/sail build --no-cache
vendor/bin/sail up -d
```

### Erro: "Authentication failed"

Verifique se as credenciais no `.env` correspondem às configuradas no `docker-compose.yml`:

```ini
MONGODB_USERNAME=sail
MONGODB_PASSWORD=password
```

### Container não inicia

Verifique os logs:

```bash
vendor/bin/sail logs mongodb
```

## Recursos Adicionais

-   [Documentação Laravel MongoDB](https://www.mongodb.com/docs/drivers/php/laravel-mongodb/)
-   [Laravel Database Documentation](https://laravel.com/docs/12.x/database#mongodb)
-   [MongoDB PHP Extension](https://www.php.net/manual/en/set.mongodb.php)

## Arquitetura

```
┌─────────────────────────────────────────┐
│         Laravel Application             │
│  ┌───────────┐         ┌─────────────┐ │
│  │ PostgreSQL│         │   MongoDB   │ │
│  │ (Relacional)        │ (Documentos) │ │
│  │                     │              │ │
│  │ • Users             │ • Logs       │ │
│  │ • Tenants           │ • Analytics  │ │
│  │ • Events            │ • Activities │ │
│  │ • Tickets           │ • Cache      │ │
│  └───────────┘         └─────────────┘ │
└─────────────────────────────────────────┘
```

## Próximos Passos

1. ✅ Instalar pacote `mongodb/laravel-mongodb`
2. ✅ Atualizar arquivo `.env` com as credenciais
3. ✅ Reiniciar containers
4. ✅ Criar seu primeiro Model MongoDB
5. ✅ Testar conexão com Tinker
