<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Создать'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
          OrderResource\Widgets\OrderStats::class
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Все'),
            'new' => Tab::make('Новые')->query(fn($query) => $query->where('status', 'new')),
            'processing' => Tab::make('В процессе')->query(fn($query) => $query->where('status', 'processing')),
            'shipped' => Tab::make('Отправлен')->query(fn($query) => $query->where('status', 'shipped')),
            'delivered' => Tab::make('Доставлен')->query(fn($query) => $query->where('status', 'delivered')),
            'cancelled' => Tab::make('Отмена')->query(fn($query) => $query->where('status', 'cancelled')),



        ];
    }
}
