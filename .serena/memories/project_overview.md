# labSIS-KIT
 
## Propósito
SaaS starter kit pronto para produção utilizando Laravel 12 e Filament 4, com arquitetura multi-painel, autenticação unificada e gestão de mídia.
 
## Tech Stack
- **Backend**: PHP 8.5.1, Laravel 12.
- **Frontend**: Filament 4, Livewire 3, Flux UI, Tailwind CSS 4 (CSS-first).
- **Ambiente**: Laravel Sail (Docker).
- **Qualidade**: Pest 4 (Tests), Larastan 3 (Static Analysis), Rector 2 (Refactoring), Pint (Formatting).
 
## Comandos de Desenvolvimento
- **Start**: `vendor/bin/sail up -d`
- **Tinker**: `vendor/bin/sail artisan tinker`
- **Tests**: `vendor/bin/sail artisan test`
- **Formatting**: `vendor/bin/sail bin pint --dirty`
- **Assets**: `vendor/bin/sail npm run dev` | `vendor/bin/sail npm run build`
 
## Convenções
- PSR-12 para formatação.
- Strict typing (`declare(strict_types=1);`) em todos os arquivos.
- Type-hinting obrigatório em parâmetros e retornos.
- Constructor property promotion sempre que possível.
- Nomes de tabelas pivot em ordem alfabética.
