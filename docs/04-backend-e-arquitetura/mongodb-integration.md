# Integração MongoDB - Auditoria e Logs

## 📋 Visão Geral

Este projeto utiliza **arquitetura híbrida** com PostgreSQL e MongoDB para otimizar performance e escalabilidade:

- **PostgreSQL**: Dados relacionais críticos (Users, Teams, Permissions)
- **MongoDB**: Logs de auditoria e dados que crescem rapidamente

## ⚙️ Configuração

### 1. Docker Compose

```yaml
# docker-compose.yml
mongodb:
    image: "mongo:latest"
    ports:
        - "${FORWARD_MONGODB_PORT:-27017}:27017"
    environment:
        MONGO_INITDB_ROOT_USERNAME: "${MONGODB_USERNAME:-sail}"
        MONGO_INITDB_ROOT_PASSWORD: "${MONGODB_PASSWORD:-password}"
    volumes:
        - "sail-mongodb:/data/db"
    networks:
        - sail
```

### 2. Variáveis de Ambiente

```ini
# .env
MONGODB_URI=mongodb://sail:password@mongodb:27017
MONGODB_USERNAME=sail
MONGODB_PASSWORD=password
MONGODB_DATABASE=labsis
FORWARD_MONGODB_PORT=27017
```

### 3. Configuração Laravel

```php
// config/database.php
'mongodb' => [
    'driver' => 'mongodb',
    'dsn' => env('MONGODB_URI', 'mongodb://localhost:27017'),
    'database' => env('MONGODB_DATABASE', 'labsis'),
],
```

## 📝 Caso de Uso: Logs de Autenticação

### Model MongoDB

```php
// app/Models/AuthenticationLog.php
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
            'login_successful' => 'boolean',
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
        ];
    }

    // Relacionamento híbrido com PostgreSQL
    public function authentications()
    {
        return $this->morphMany(
            AuthenticationLog::class,
            'authenticatable'
        )->latest('login_at');
    }
}
```

### Estrutura de Documento

```json
{
  "_id": ObjectId("69693240e77cefee61017852"),
  "authenticatable_type": "App\\Models\\User",
  "authenticatable_id": 1,
  "ip_address": "192.168.1.100",
  "user_agent": "Mozilla/5.0...",
  "login_at": ISODate("2026-01-20T00:35:20Z"),
  "login_successful": true,
  "logout_at": null
}
```

### Resource Filament

```php
// app/Filament/Resources/Authentication/Tables/AuthenticationLogTable.php
TextColumn::make('authenticatable_id')
    ->label('Usuário')
    ->formatStateUsing(function ($state, AuthenticationLog $record) {
        // ⚠️ Carregamento manual para relacionamento híbrido
        $user = User::find($record->authenticatable_id);

        return $user?->name ?? 'Desconhecido';
    })
    ->searchable(isGlobal: false, isIndividual: true),
```

## 🎯 Quando Usar Cada Banco

### ✅ Use PostgreSQL Para:

| Caso de Uso               | Razão                                    |
| ------------------------- | ---------------------------------------- |
| **Users, Teams, Roles** | Integridade referencial, transações ACID |
| **Permissions, Teams**    | Relacionamentos complexos (N:N)          |
| **Media, Posts**          | Dados estruturados com foreign keys      |
| **Dados financeiros**     | Precisão e consistência críticas         |

### ✅ Use MongoDB Para:

| Caso de Uso              | Razão                                        |
| ------------------------ | -------------------------------------------- |
| **Logs de Autenticação** | Alto volume de escritas, schema flexível     |
| **Activity Logs**        | Crescimento rápido, queries temporais        |
| **Analytics/Métricas**   | Agregações complexas, dados não estruturados |
| **API Request Logs**     | Performance em inserções massivas            |

## 💡 Benefícios Esperados

### Performance

- **Escritas 3-5x mais rápidas**: MongoDB otimizado para inserções
- **Queries temporais eficientes**: Índices em datas (TTL automático)
- **Sem bloqueios de tabela**: PostgreSQL não afetado por logs

### Escalabilidade

- **Schema flexível**: Adicionar campos sem migrations
- **Sharding nativo**: Distribuição horizontal quando necessário
- **TTL Index**: Auto-deletar logs antigos (ex: após 365 dias)

### Manutenção

- **Separação de concerns**: Auditoria isolada de dados críticos
- **Backups independentes**: Estratégias diferentes por banco
- **Migrations independentes**: `migrate:fresh` não afeta MongoDB

## ⚠️ Relacionamentos Híbridos

O Laravel **não suporta eager loading** entre PostgreSQL e MongoDB. Solução:

```php
// ❌ NÃO funciona
TextColumn::make('authenticatable.name')

// ✅ Funciona - Carregamento manual
TextColumn::make('authenticatable_id')
    ->formatStateUsing(fn ($state, $record) =>
        User::find($record->authenticatable_id)?->name ?? 'Desconhecido'
    )
```

## 📚 Recursos


- [MongoDB Laravel Driver](https://www.mongodb.com/docs/drivers/php/laravel-mongodb/)
- [Laravel MongoDB](https://laravel.com/docs/12.x/mongodb)
- [Config: Database](/config/database.php)
