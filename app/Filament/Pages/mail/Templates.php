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

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Emails';

    protected static ?string $title = 'Templates de E-mail';

    protected static string|\UnitEnum|null $navigationGroup = 'Administração';

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
                'name' => 'Template de E-mail',
                'description' => 'Template padrão utilizado para todos os e-mails do sistema',
                'size' => number_format($file->getSize() / 1024, 2).' KB',
                'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
            ],
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->getTemplates())
            ->columns([
                TextColumn::make('name')->label('Nome')->sortable(),
                TextColumn::make('description')->label('Descrição'),
                TextColumn::make('size')->label('Tamanho')->sortable(),
                TextColumn::make('last_modified')->label('Modificado em')->dateTime('Y-m-d H:i:s')->sortable(),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(PreviewTemplate::getUrl()),
            ]);
    }
}
