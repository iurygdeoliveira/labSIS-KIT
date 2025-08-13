<?php

declare(strict_types=1);

namespace App\Filament\Configurators;

use Filament\Forms\Components\Field;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FilamentComponentsConfigurator
{
    public static function configure(): void
    {
        Field::configureUsing(function (Field $field): void {
            $field->translateLabel();
        });

        Column::configureUsing(function (Column $column): void {
            $column->translateLabel();
        });

        IconColumn::configureUsing(function (IconColumn $iconColumn): void {
            $iconColumn
                ->alignment(Alignment::Center)
                ->verticalAlignment(VerticalAlignment::Center);
        });

        TextColumn::configureUsing(function (TextColumn $textColumn): void {
            $textColumn->wrap();
        });

        CheckboxColumn::configureUsing(function (CheckboxColumn $checkboxColumn): void {
            $checkboxColumn
                ->alignment(Alignment::Center)
                ->verticalAlignment(VerticalAlignment::Center);
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->persistSortInSession()
                ->extremePaginationLinks()
                ->defaultPaginationPageOption(20)
                ->paginated([20, 40, 60, 80, 'all'])
                ->emptyStateIcon('heroicon-s-exclamation-triangle');
        });
    }
}
