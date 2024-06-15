<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Faker\Provider\en_UG\PhoneNumber;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Forms\Components\Section::make('Информация заказа')->schema([
                        Forms\Components\Select::make('user_id')
                        ->label('Клиент')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->label('Метод оплаты')
                        ->options([
                            'stripe' => 'Онлайн оплата',
                            'cod' => 'Оплата наличными'
                        ])
                            ->default('cod')

                            ->required(),

                        Select::make('payment_status')
                            ->label('Статус платежа')
                        ->options([
                            'pending' => 'Ожидаемый',
                            'paid' => 'Оплачиваемый',
                            'failed' => 'Отмена'
                        ])
                        ->default('pending')
                        ->required(),

                        Forms\Components\ToggleButtons::make('status')
                            ->label('Статус заказа')
                            ->inline()
                            ->default('new')
                            ->required()
                        ->options([
                            'new' => 'Новый',
                            'processing' => 'В процессе',
                            'shipped' => 'Отправленный',
                            'delivered' => 'Доставлен',
                            'cancelled' => 'Отмена'
                        ])
                        ->colors([
                            'new' => 'info',
                            'processing' => 'warning',
                            'shipped' => 'success',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ])
                        ->icons([
                            'new' => 'heroicon-m-sparkles',
                            'processing' => 'heroicon-m-arrow-path',
                            'shipped' => 'heroicon-m-truck',
                            'delivered' => 'heroicon-m-check-badge',
                            'cancelled' => 'heroicon-m-x-circle'
                        ]),

                        Select::make('currency')
                            ->label('Валюта')
                        ->options([
                            'rub'=>'RUB'
                        ])
                        ->default('rub')
                        ->required(),

                        Select::make('shipping_method')
                            ->label('Метод доставки')
                        ->options([
                            'fedex' => 'СДЭК',
                            'ups' =>'Авито доставка',
                            'dhl' => 'Почта России',
                        ])
                        ->default('fedex'),

                        Textarea::make('notes')
                            ->label('Запись')
                        ->columnSpanFull()

                    ])->columns(2),

                    Forms\Components\Section::make('Корзина заказа')->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Заказ')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                                ->label('Заказ')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->columnSpanFull(4)
                            ->reactive()
                            ->afterStateUpdated(fn($state, Forms\Set $set) => $set('unit_amount', Product::find
                            ($state)?->price ?? 0))
                            ->afterStateUpdated(fn($state, Forms\Set $set) => $set('total_amount', Product::find
                            ($state)?->price ?? 0)),


                            TextInput::make('quantity')
                                ->label('Количество')
                            ->numeric()
                                ->required()
                            ->default(1)
                            ->minValue(1)
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) => $set('total_amount',
                                $state*$get('unit_amount'))),

                            TextInput::make('unit_amount')
                                ->label('Цена за ед.')
                                ->numeric()
                            ->required()
                            ->disabled()
                                ->dehydrated()
                            ->columnSpan(3),

                            TextInput::make('total_amount')
                                ->label('Итого')
                            ->numeric()
                            ->required()
                                ->dehydrated()
                            ->columnSpan(3),
                        ])->columns(12),
                        Placeholder::make('grand_total_placeholder')
                        ->label('Итого')
                        ->content(function (Forms\Get $get, Forms\Set $set){
                            $total = 0;
                            if(!$repeaters = $get('items')){
                                return $total;
                            }
                            foreach($repeaters as $key => $repeater){
                                $total += $get("items.{$key}.total_amount");
                            }
                            $set('grand_total', $total);
                            return Number::currency($total, 'RUB');
                        }),

                        Forms\Components\Hidden::make('grand_total')
                        ->default(0)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Клиент')
                ->sortable()
                ->searchable(),

                TextColumn::make('grand_total')
                    ->label('Общая сумма')
                    ->numeric()
                    ->sortable()
                    ->money('rub'),


                TextColumn::make('currency')
                    ->label('Валюта')
                ->searchable()
                ->sortable(),



                Tables\Columns\SelectColumn::make('status')
                ->options([
                    'new' => 'новый',
                    'processing' => 'в обработке',
                    'shipped' => 'отправлено',
                    'delivered' => 'доставлен',
                    'cancelled' => 'отмена'
                ])
                ->searchable()
                ->sortable(),


                TextColumn::make('created_at')
                    ->label('Создано')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('update_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Обзор'),
                    Tables\Actions\EditAction::make()->label('Изменить'),
                    Tables\Actions\DeleteAction::make()->label('Удалить'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AddressRelationManager::class
        ];
    }

    public static function  getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function  getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'danger' : 'success';

    }

    public static function getNavigationLabel(): string
    {
        return 'Заказы';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
