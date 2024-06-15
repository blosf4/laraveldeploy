<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    public static ?string $label = '';

    protected int | string | array $columnSpan = "full";

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)

            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Номер заказа')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Клиент')
                ->searchable(),

                TextColumn::make('grand_total')
                    ->label('Итого')
                    ->money('RUB'),

                TextColumn::make('status')

                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state):string=>match ($state) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->icon(fn (string $state):string=>match ($state) {
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',

                    })->sortable(),


                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
            ])->actions([
                Tables\Actions\Action::make('View order')
                    ->label('Обзор')
                ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                ->icon('heroicon-s-eye')
            ]);
    }
}
