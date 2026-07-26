<?php

declare(strict_types=1);

namespace App\Filament\Configurators;

use Filament\Forms\Components\Field;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class FilamentComponentsConfigurator
{
    public static function configure(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => view('filament.components.mobile-bottom-nav')->render(),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE,
            fn (): string => Blade::render(
                "@if (filament()->auth()->check() && filament()->hasDatabaseNotifications()) @livewire(filament()->getDatabaseNotificationsLivewireComponent(), ['lazy' => filament()->hasLazyLoadedDatabaseNotifications()]) @endif"
            ),
        );

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
                ->deferLoading()
                ->persistSortInSession()
                ->persistSearchInSession()
                ->extremePaginationLinks()
                ->defaultPaginationPageOption(20)
                ->paginated([20, 40, 60, 80, 'all'])
                ->stackedOnMobile()
                ->emptyStateIcon(Heroicon::ExclamationTriangle);
        });
    }
}
