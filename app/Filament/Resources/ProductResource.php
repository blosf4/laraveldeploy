<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\View\View;
use Symfony\Component\Console\Descriptor\MarkdownDescriptor;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make('Информация о продукте')->schema([
                        TextInput::make('name')
                            ->label('Название')
                        ->required()
                        ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Путь')
                        ->required()
                        ->dehydrated()
                        ->maxLength(255),

                        MarkdownEditor::make('description')
                            ->label('Описание')
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory('products')
                    ])->columns(2),

                    Section::make('Картинки')->schema([
                        FileUpload::make('images')
                        ->multiple()
                        ->directory('products')
                        ->maxFiles(5)
                        ->reorderable()
                    ])


                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make('Цена')->schema([
                        TextInput::make('price')
                        ->numeric()
                        ->required()
                        ->prefix('rub')
                    ]),

                    Section::make('Связь')->schema([
                        Select::make('category_id')
                            ->label('Категория')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('category', 'name'),

                    Select::make('brand_id')
                        ->label('Бренд')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('brand', 'name'),
                    ]),

                    Section::make('Статус')->schema([
                        Toggle::make('in_stock')
                            ->label('В наличии')
                            ->required()
                            ->default(true),
                        Toggle::make('in_active')
                            ->label('Активен')
                            ->required()
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('Продвижение')
                            ->required(),
                        Toggle::make('on_sale')
                            ->label('В продаже')
                            ->required()
                    ])
                ])->columns(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->label('Название'),
                TextColumn::make('category.name')->sortable()->label('Категория'),
                TextColumn::make('brand.name')->sortable()->label('Бренд'),
                TextColumn::make('price')->money('RUB')->sortable()->label('Цена'),

                IconColumn::make('is_featured')->boolean()->label('Продвижение'),
                IconColumn::make('on_sale')->boolean()->label('В продаже'),
                IconColumn::make('in_stock')->boolean()->label('В наличии'),
                IconColumn::make('is_active')->boolean()->label('Активен'),

                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)->label('Создано'),
                TextColumn::make('update_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)->label('Обновлено'),



            ])
            ->filters([
                SelectFilter::make('category')->relationship('category', 'name')->label('Категория'),

                SelectFilter::make('brand')->relationship('brand', 'name')->label('Название'),

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
                    Tables\Actions\DeleteBulkAction::make()->label('Удалить'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Продукты';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
