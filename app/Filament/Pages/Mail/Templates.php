<?php

declare(strict_types=1);

namespace App\Filament\Pages\Mail;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Templates extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-s-envelope';

    protected static ?string $navigationLabel = 'Emails';

    protected static ?string $title = 'Templates de E-mail';

    protected static ?int $navigationSort = 2;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $slug = 'mail-templates';

    protected string $view = 'filament.pages.mail.templates';

    public string $codeModalContent = '';

    public string $codeModalTitle = '';

    protected function getTemplates(): Collection
    {
        $path = resource_path('views/vendor/mail/html/message.blade.php');

        if (! File::exists($path)) {
            return collect();
        }

        $file = new \SplFileInfo($path);

        return collect([
            [
                'id' => 'password-reset',
                'name' => 'Redefinição de Senha',
                'description' => 'Notificação enviada quando um usuário solicita reset de senha',
                'size' => number_format($file->getSize() / 1024, 2).' KB',
                'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'path' => $path,
            ],
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): \Illuminate\Support\Collection => $this->getTemplates())
            ->columns([
                TextColumn::make('name')->label('Nome')->sortable(),
                TextColumn::make('description')->label('Descrição'),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-s-eye')
                    ->color('primary')
                    ->url(fn (array $record): string => PreviewTemplate::getUrl(['type' => $record['id']])),
            ]);
    }
}
