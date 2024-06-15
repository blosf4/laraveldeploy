<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public static ?string $label = '';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
               //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([

                TextColumn::make('id')
                ->label('Номер заказа')
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

                TextColumn::make('payment_method')
                    ->label('Способ оплаты')
                ->sortable()
                ->searchable(),

                TextColumn::make('payment_status')
                    ->label('Статус оплаты')
                ->sortable()
                    ->badge()
                ->searchable(),

                TextColumn::make('created_at')

                ->label('Создано')
                ->dateTime()
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Просмотр заказа')
                ->url(fn (Order $record):string=>OrderResource::getUrl('view', ['record' => $record]))
                ->color('info')
                ->icon('heroicon-s-eye'),
                Tables\Actions\DeleteAction::make()->label('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Удалить'),
                ]),
            ]);
    }
}
