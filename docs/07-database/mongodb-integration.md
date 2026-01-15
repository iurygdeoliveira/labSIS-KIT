# Integração MongoDB - Logs de Autenticação

## Visão Geral

Este projeto utiliza **MongoDB** paralelamente ao **PostgreSQL** para otimizar o armazenamento de dados de auditoria que crescem rapidamente e não necessitam de relacionamentos complexos. O caso de uso principal é o armazenamento de **logs de autenticação** dos usuários.

## Arquitetura Híbrida

```
┌─────────────────────────────────────────┐
│         Laravel Application             │
│  ┌───────────┐         ┌─────────────┐ │
│  │ PostgreSQL│         │   MongoDB   │ │
│  │ (Relacional)        │ (Documentos) │ │
│  │                     │              │ │
│  │ • Users             │ • Auth Logs │ │
│  │ • Tenants           │ • Analytics │ │
│  │ • Permissions       │ • Activities│ │
│  │ • Teams             │ (futuro)    │ │
│  └───────────┘         └─────────────┘ │
└─────────────────────────────────────────┘
```

## Stack Tecnológica MongoDB

-   **Banco de Dados**: MongoDB Atlas Local (via Docker)
-   **Driver PHP**: `php8.5-mongodb` (pré-instalado no container Sail)
-   **Pacote Laravel**: `mongodb/laravel-mongodb` v5.5
-   **Porta**: 27017
-   **Autenticação**: Ativada (usuário: `sail`, senha: `password`)

## Por que MongoDB para Logs de Autenticação?

### ✅ Vantagens

1. **Performance em Escrita**: MongoDB é otimizado para alto volume de inserções
2. **Schema Flexível**: Permite adicionar campos sem migrations
3. **Escalabilidade Horizontal**: Facilita sharding quando necessário
4. **TTL Automático**: Suporte nativo para expiração de documentos
5. **Queries Eficientes**: Índices otimizados para buscas temporais

### ❌ Quando NÃO usar MongoDB

-   Dados que requerem transações ACID complexas
-   Relacionamentos complexos entre tabelas
-   Necessidade de integridade referencial rigorosa
-   Estruturas de dados muito fixas

## Configuração

### Variáveis de Ambiente

No `.env`:

```ini
# MongoDB
MONGODB_URI=mongodb://sail:password@mongodb:27017
MONGODB_USERNAME=sail
MONGODB_PASSWORD=password
MONGODB_DATABASE=labsis
FORWARD_MONGODB_PORT=27017
```

### Configuração do Laravel

Em `config/database.php`:

```php
'mongodb' => [
    'driver' => 'mongodb',
    'dsn' => env('MONGODB_URI', 'mongodb://localhost:27017'),
    'database' => env('MONGODB_DATABASE', 'labsis'),
],
```

### Configuração do Authentication Log

Em `config/authentication-log.php`:

```php
return [
    'model' => \App\Models\AuthenticationLog::class,
    'table_name' => 'authentication_log',
    'db_connection' => 'mongodb', // ← Usa MongoDB
    // ...
];
```

## Model Customizado

O Model `App\Models\AuthenticationLog` sobrescreve o padrão do pacote `rappasoft/laravel-authentication-log`:

```php
<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AuthenticationLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'authentication_log';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'cleared_by_user' => 'boolean',
            'location' => 'array',
            'login_successful' => 'boolean',
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
        ];
    }

    // Scopes personalizados
    public function scopeSuccessful($query) { /*...*/ }
    public function scopeFailed($query) { /*...*/ }
    public function scopeRecent($query, int $days = 7) { /*...*/ }
    public function scopeActive($query) { /*...*/ }
}
```

## Uso no Filament

O Resource `CustomAuthenticationLogResource` funciona normalmente, pois o MongoDB Laravel mantém compatibilidade com o Eloquent:

```php
// Em app/Filament/Resources/Authentication/CustomAuthenticationLogResource.php
public static function getEloquentQuery(): Builder
{
    // Queries funcionam normalmente
    return parent::getEloquentQuery()
        ->with('authenticatable')
        ->whereHasMorph('authenticatable', [User::class], ...);
}
```

## Estrutura de Documentos

Cada log de autenticação é armazenado como um documento MongoDB:

```json
{
  "_id": ObjectId("69693240e77cefee61017852"),
  "authenticatable_type": "App\\Models\\User",
  "authenticatable_id": 1,
  "ip_address": "192.168.1.100",
  "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) ...",
  "login_at": ISODate("2026-01-15T18:35:20Z"),
  "login_successful": true,
  "logout_at": null,
  "cleared_by_user": false,
  "location": {
    "country": "BR",
    "city": "São Paulo"
  }
}
```

## Queries e Scopes

### Buscar Logs Recentes

```php
use App\Models\AuthenticationLog;

// Logs dos últimos 7 dias
$recentLogs = AuthenticationLog::recent(7)->get();

// Logs dos últimos 30 dias
$monthlyLogs = AuthenticationLog::recent(30)->get();
```

### Logs Bem-Sucedidos vs Falhos

```php
// Apenas logins bem-sucedidos
$successful = AuthenticationLog::successful()->get();

// Apenas logins falhados
$failed = AuthenticationLog::failed()->get();
```

### Sessões Ativas

```php
// Usuários ainda logados (sem logout)
$activeSessions = AuthenticationLog::active()->get();
```

### Buscar por Usuário

```php
// Via relacionamento polimórfico
$user = User::find(1);
$userLogs = $user->authentications;

// Ou diretamente
$userLogs = AuthenticationLog::where('authenticatable_id', 1)
    ->where('authenticatable_type', User::class)
    ->get();
```

## Comandos Úteis

### Acessar MongoDB Shell

```bash
vendor/bin/sail mongodb mongosh -u sail -p password --authenticationDatabase admin
```

### Ver Collections e Documentos

```javascript
// No mongosh
use labsis
show collections
db.authentication_log.find().pretty().limit(5)
```

### Contar Documentos

```javascript
db.authentication_log.countDocuments();
db.authentication_log.countDocuments({ login_successful: true });
```

### Criar Índices (Performance)

```javascript
// Índice para buscar por usuário
db.authentication_log.createIndex({ authenticatable_id: 1 });

// Índice para buscar por data
db.authentication_log.createIndex({ login_at: -1 });

// Índice composto
db.authentication_log.createIndex({
    authenticatable_id: 1,
    login_at: -1,
});
```

### TTL Index (Auto-delete)

Para deletar logs automaticamente após X dias:

```javascript
// Deletar logs após 365 dias
db.authentication_log.createIndex(
    { login_at: 1 },
    { expireAfterSeconds: 31536000 } // 365 dias
);
```

## Limpeza Manual de Logs

### Via Artisan (Pacote)

```bash
vendor/bin/sail artisan authentication-log:purge
```

### Via Tinker

```bash
vendor/bin/sail artisan tinker
```

```php
// Deletar logs com mais de 1 ano
AuthenticationLog::where('login_at', '<', now()->subYear())->delete();

// Deletar todos os logs falhados
AuthenticationLog::failed()->delete();
```

## Métricas e Analytics

### Total de Logins por dia

```php
use Illuminate\Support\Facades\DB;

$dailyLogins = DB::connection('mongodb')
    ->collection('authentication_log')
    ->raw(function ($collection) {
        return $collection->aggregate([
            [
                '$group' => [
                    '_id' => [
                        '$dateToString' => [
                            'format' => '%Y-%m-%d',
                            'date' => '$login_at'
                        ]
                    ],
                    'count' => ['$sum' => 1]
                ]
            ],
            ['$sort' => ['_id' => -1]],
            ['$limit' => 30]
        ]);
    });
```

### Taxa de sucesso de login

```php
$total = AuthenticationLog::count();
$successful = AuthenticationLog::successful()->count();
$successRate = ($successful / $total) * 100;

echo "Taxa de sucesso: {$successRate}%";
```

## Migração de Dados SQL → MongoDB

Se você tinha logs no PostgreSQL e quer migrar:

```php
// Script de migração (executar uma vez)
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog as OldLog;
use App\Models\AuthenticationLog as NewLog;

DB::connection('pgsql')
    ->table('authentication_log')
    ->orderBy('id')
    ->chunk(500, function ($logs) {
        foreach ($logs as $log) {
            NewLog::create((array) $log);
        }
    });
```

## Troubleshooting

### Erro: Class 'MongoDB\Driver\Manager' not found

Reconstruir containers:

```bash
vendor/bin/sail down
vendor/bin/sail build --no-cache
vendor/bin/sail up -d
```

### Erro: Failed to connect to MongoDB

Verificar se o container está rodando:

```bash
vendor/bin/sail ps
vendor/bin/sail logs mongodb
```

### Logs não estão sendo salvos no MongoDB

1. Verificar configuração em `config/authentication-log.php`
2. Limpar cache de configuração:

```bash
vendor/bin/sail artisan config:clear
```

### Performance lenta em queries

Criar índices adequados (ver seção "Criar Índices" acima).

## Próximos Passos

### Expandir uso do MongoDB

Considere usar MongoDB para:

-   **Activity Logs**: Logs genéricos de atividades do sistema
-   **Analytics**: Métricas e eventos de uso
-   **Notifications History**: Histórico de notificações enviadas
-   **API Request Logs**: Logs de requisições à API

### Exemplo: Activity Log

```php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ActivityLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'metadata',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'performed_at' => 'datetime',
        ];
    }
}
```

## Recursos Adicionais

-   [MongoDB Laravel Documentation](https://www.mongodb.com/docs/drivers/php/laravel-mongodb/)
-   [Laravel Database Documentation](https://laravel.com/docs/12.x/database#mongodb)
-   [MongoDB PHP Extension](https://www.php.net/manual/en/set.mongodb.php)
-   [Laravel Authentication Log](https://github.com/rappasoft/laravel-authentication-log)

## Conclusão

A integração MongoDB neste projeto demonstra uma arquitetura híbrida eficiente, utilizando o melhor de cada tecnologia:

-   **PostgreSQL**: Para dados relacionais críticos
-   **MongoDB**: Para logs de auditoria e dados não estruturados

Esta abordagem garante performance, escalabilidade e economia de recursos.
