<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Новые заказы', Order::query()->where('status', 'new')->count()),
            Stat::make('В обработке', Order::query()->where('status', 'processing')->count()),
            Stat::make('Доставлено', Order::query()->where('status', 'snipped')->count()),
            Stat::make('Итого', Number::currency(Order::query()->avg('grand_total'), 'RUB')),


        ];
    }
}
