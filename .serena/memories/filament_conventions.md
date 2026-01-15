# Convenções Filament 4

## Resource Creation

### Command
```bash
# Sempre usar --view para evitar prompts interativos
./vendor/bin/sail artisan make:filament-resource Post --view
```

## Schemas vs Components

Filament 4 moveu componentes de layout para `Filament\Schemas\Components`:

```php
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
```

## Actions

Todas as actions estendem `Filament\Actions\Action` (não mais `Filament\Tables\Actions`).

## Testing

```php
// Table testing
livewire(ListUsers::class)
    ->assertCanSeeTableRecords($users)
    ->searchTable('john')
    ->assertCanSeeTableRecords($filtered);

// Form testing
livewire(CreatePost::class)
    ->fillForm(['title' => 'Test', 'content' => 'Content'])
    ->call('create')
    ->assertNotified()
    ->assertRedirect();

// Actions
livewire(EditInvoice::class, ['invoice' => $invoice])
    ->callAction('send');
```

## Relationships

Use método `relationship()` em selects/checkboxes:

```php
Select::make('user_id')
    ->label('Author')
    ->relationship('author')  // Busca modelo via relationship
    ->required();
```

## Breaking Changes v3 → v4

- **File visibility**: Agora `private` por default
- **Defer filters**: Agora padrão (botão Apply necessário)
- **Grid/Section**: Não ocupam todas as colunas por default
- **Pagination "all"**: Não disponível por padrão

##Key Helpers

- `make()`: Inicializa componentes
- `->relationship()`: Para selects/forms
- `->schema()`: Define estrutura de form/infolist

---

Gerado de: GEMINI.md (filament/filament rules)
