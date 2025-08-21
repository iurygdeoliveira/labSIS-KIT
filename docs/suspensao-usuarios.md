# Sistema de Suspensão de Usuários no Filament

## 📋 Índice

- [Introdução](#introdução)
- [Como Funciona a Suspensão](#como-funciona-a-suspensão)
- [Arquitetura da Implementação](#arquitetura-da-implementação)
- [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
- [Modelo User](#modelo-user)
- [Interface do Filament](#interface-do-filament)
- [Lógica de Negócio](#lógica-de-negócio)
- [Segurança e Validações](#segurança-e-validações)
- [Troubleshooting](#troubleshooting)
- [Conclusão](#conclusão)

## Introdução

Este projeto implementa um sistema completo de **Suspensão de Usuários** no Filament PHP. O sistema permite que administradores suspendam contas de usuários por motivos específicos, impedindo o acesso ao painel administrativo e fornecendo feedback claro sobre o status da conta.

## Como Funciona a Suspensão

A suspensão de usuários funciona através de um sistema de flags e timestamps:

1. **Flag de Suspensão**: Campo `is_suspended` (boolean) indica se o usuário está suspenso
2. **Timestamp de Suspensão**: Campo `suspended_at` registra quando a suspensão foi aplicada
3. **Motivo da Suspensão**: Campo `suspension_reason` armazena o motivo da suspensão
4. **Bloqueio de Acesso**: Usuários suspensos não conseguem acessar o painel

## Arquitetura da Implementação

### 1. Estrutura do Banco de Dados

A tabela `users` possui campos específicos para gerenciar suspensões:

```php
// database/migrations/0001_01_01_000000_create_users_table.php

Schema::create('users', function (Blueprint $table) {
    // ... outros campos
    $table->boolean('is_suspended')->default(false);        // Flag de suspensão
    $table->timestamp('suspended_at')->nullable();          // Data/hora da suspensão
    $table->text('suspension_reason')->nullable();          // Motivo da suspensão
    // ... outros campos
});
```

### 2. Modelo User

O modelo `User` implementa a lógica de suspensão:

```php
// app/Models/User.php

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'avatar_url',
        'email_verified_at',
        'is_suspended',           // Campo de suspensão
        'suspended_at',           // Data da suspensão
        'suspension_reason',      // Motivo da suspensão
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_suspended' => 'boolean',           // Cast para boolean
            'suspended_at' => 'datetime:d/m/Y H:i', // Cast para datetime
            // ... outros casts
        ];
    }

    // Método para verificar se o usuário está suspenso
    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    // Controle de acesso ao painel
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->hasVerifiedEmail()) {
            return false;
        }

        if ($this->isSuspended()) {
            return false;
        }

        return true;
    }
}
```

## Interface do Filament

### 1. Tabela de Usuários

A tabela exibe o status de suspensão com badges coloridos:

```php
// app/Filament/Resources/Users/Tables/UsersTable.php

use Filament\Tables\Columns\TextColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => $record->getFilamentAvatarUrl())
                    ->extraImgAttributes(['alt' => 'Avatar do usuário']),
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address'),
                TextColumn::make('is_suspended')
                    ->label('Status')
                    ->formatStateUsing(fn (User $record): string => 
                        $record->is_suspended ? __('Suspenso') : __('Autorizado')
                    )
                    ->badge()
                    ->color(fn (User $record): string => 
                        $record->is_suspended ? 'danger' : 'success'
                    )
                    ->icon(fn (User $record): string => 
                        $record->is_suspended ? 'heroicon-c-no-symbol' : 'heroicon-c-check'
                    )
                    ->alignCenter(),
            ]);
    }
}
```

### 2. Formulário de Edição

O formulário permite gerenciar a suspensão com validações:

```php
// app/Filament/Resources/Users/Schemas/UserForm.php

use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ... outros campos
                
                Toggle::make('is_suspended')
                    ->label(fn (Get $get): string => 
                        $get('is_suspended') ? 'Usuário não autorizado' : 'Usuário autorizado'
                    )
                    ->onColor('danger')
                    ->offColor('success')
                    ->onIcon('heroicon-c-no-symbol')
                    ->offIcon('heroicon-c-check')
                    ->default(fn ($record): bool => (bool) ($record?->is_suspended))
                    ->disabled(fn (?User $record): bool => 
                        $record?->getKey() === Auth::id()
                    )
                    ->hint(fn (?User $record): ?string => 
                        $record?->getKey() === Auth::id() 
                            ? __('Você não pode suspender a si mesmo.') 
                            : null
                    )
                    ->hintColor('danger')
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                
                TextInput::make('suspension_reason')
                    ->label('Motivo da suspensão')
                    ->disabled(fn (?User $record): bool => 
                        $record?->getKey() === Auth::id()
                    )
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ]);
    }
}
```

### 3. Página de Edição

A página de edição implementa a lógica de negócio para suspensão:

```php
// app/Filament/Resources/Users/Pages/EditUser.php

use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected function getHeaderActions(): array
    {
        $actions = [
            $this->getBackButtonAction(),
            ViewAction::make(),
        ];

        // Só mostra o botão de deletar se não for o usuário logado
        if ($this->record->getKey() !== Auth::id()) {
            $actions[] = DeleteAction::make()
                ->successNotification(Notification::make())
                ->after(fn () => $this->notifySuccess('Usuário excluído com sucesso.'));
        }

        return $actions;
    }

    protected function afterSave(): void
    {
        // Sincroniza suspended_at com is_suspended
        if ($this->record->is_suspended && $this->record->suspended_at === null) {
            $this->record->suspended_at = now();
            $this->record->save();
        }

        if (! $this->record->is_suspended && $this->record->suspended_at !== null) {
            $this->record->suspended_at = null;
            $this->record->save();
        }

        // Previne auto-suspensão
        if ($this->record->getKey() === Auth::id() && $this->record->is_suspended) {
            $this->record->forceFill([
                'is_suspended' => false,
                'suspended_at' => null,
            ])->save();

            $this->notifyDanger('Você não pode suspender a si mesmo. Alteração revertida.');
            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }

        $this->notifySuccess('Usuário atualizado com sucesso.');
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
```

### 4. Infolist de Usuário

A visualização detalhada organiza as informações de suspensão em abas:

```php
// app/Filament/Resources/Users/Schemas/UserInfolist.php

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User Details')
                    ->tabs([
                        // ... outras abas
                        
                        Tab::make('Suspensão')
                            ->icon(Heroicon::NoSymbol)
                            ->schema([
                                TextEntry::make('is_suspended')
                                    ->label('Status')
                                    ->formatStateUsing(fn (?bool $state): string => 
                                        $state ? __('Suspenso') : __('Autorizado')
                                    )
                                    ->badge()
                                    ->color(fn (?bool $state): string => 
                                        $state ? 'danger' : 'success'
                                    )
                                    ->icon(fn (?bool $state): string => 
                                        $state ? 'heroicon-c-no-symbol' : 'heroicon-c-check'
                                    ),
                                TextEntry::make('suspended_at')
                                    ->label('Suspenso em')
                                    ->dateTime('d-m-Y H:i')
                                    ->placeholder('-'),
                                TextEntry::make('suspension_reason')
                                    ->label('Motivo da suspensão')
                                    ->placeholder('-'),
                            ]),
                    ])->persistTabInQueryString(),
            ]);
    }
}
```

## Lógica de Negócio

### 1. Sincronização Automática

O sistema sincroniza automaticamente os campos `is_suspended` e `suspended_at`:

- **Ao suspender**: Define `is_suspended = true` e `suspended_at = now()`
- **Ao reativar**: Define `is_suspended = false` e `suspended_at = null`

### 2. Prevenção de Auto-Suspensão

Usuários não podem suspender suas próprias contas:

```php
// Campo desabilitado para o usuário logado
->disabled(fn (?User $record): bool => $record?->getKey() === Auth::id())

// Dica explicativa
->hint(fn (?User $record): ?string => 
    $record?->getKey() === Auth::id() 
        ? __('Você não pode suspender a si mesmo.') 
        : null
)
```

### 3. Controle de Acesso

Usuários suspensos são automaticamente bloqueados do painel:

```php
public function canAccessPanel(Panel $panel): bool
{
    if ($this->isSuspended()) {
        return false;
    }
    return true;
}
```

## Segurança e Validações

### 1. Validações de Segurança

- ✅ **Auto-suspensão bloqueada**: Usuários não podem suspender a si mesmos
- ✅ **Campos protegidos**: Motivo da suspensão fica desabilitado para auto-edição
- ✅ **Redirecionamento seguro**: Após edição, usuário é redirecionado para a listagem
- ✅ **Notificações claras**: Feedback visual para todas as ações

### 2. Campos Ocultos na Criação

Os campos de suspensão só aparecem na edição:

```php
->hidden(fn (string $operation): bool => $operation === 'create')
```

### 3. Controle de Ações

Botões de ação são condicionais baseados no usuário logado:

```php
// Botão de deletar só aparece para outros usuários
if ($this->record->getKey() !== Auth::id()) {
    $actions[] = DeleteAction::make();
}
```

## Troubleshooting

### 1. Problemas Comuns

**Usuário suspenso ainda consegue acessar o painel:**
- Verifique se o método `canAccessPanel()` está sendo chamado
- Confirme se o campo `is_suspended` está sendo atualizado corretamente

**Campo `suspended_at` não está sendo preenchido:**
- Verifique se o método `afterSave()` está sendo executado
- Confirme se a lógica de sincronização está funcionando

**Toggle não está funcionando:**
- Verifique se o campo está sendo salvo corretamente no banco
- Confirme se não há conflitos de JavaScript

## Conclusão

O sistema de suspensão de usuários implementado oferece:

- 🔒 **Segurança robusta** com validações automáticas
- 🎯 **Interface intuitiva** com feedback visual claro
- 🛡️ **Proteção contra auto-suspensão** para evitar bloqueios acidentais
- 📊 **Organização clara** das informações em abas
- 🔄 **Sincronização automática** entre campos relacionados
- 🚫 **Bloqueio automático** de usuários suspensos

Este sistema garante que administradores tenham controle total sobre o acesso dos usuários ao painel, mantendo a segurança e a usabilidade da aplicação.

### 📚 **Para Mais Informações**

Para obter informações mais detalhadas sobre implementação de suspensão de contas no Laravel, consulte o artigo oficial do Laravel News:

**[Implementing Account Suspension in Laravel](https://laravel-news.com/implementing-account-suspension-in-laravel)**

Este artigo fornece insights adicionais sobre as melhores práticas e padrões recomendados para sistemas de suspensão de usuários.
