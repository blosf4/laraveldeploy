<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('first_name')
                    ->label('Имя')
                ->required()
                ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Фамилия')
                ->required()
                ->maxLength(255),

                TextInput::make('phone')
                    ->label('Телефон')
                ->required()
                ->tel()
                ->maxLength(20),

                TextInput::make('city')
                    ->label('Город')
                ->required()
                ->maxLength(255),

                TextInput::make('state')
                    ->label('Область')
                ->required()
                ->maxLength(255),

                TextInput::make('zip_code')
                    ->label('Почтовый индекс')
                ->required()
                ->numeric()
                ->maxLength(10),

                Forms\Components\Textarea::make('street_address')
                    ->label('Улица')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('street_address')
            ->columns([
                TextColumn::make('first_name')
                ->label('Имя'),
                TextColumn::make('last_name')
                    ->label('Фамилия'),
                TextColumn::make('phone')->label('Телефон'),
                TextColumn::make('city')->label('Город'),
                TextColumn::make('state')->label('Область'),
                TextColumn::make('zip_code')->label('Почтовой индекс'),
                TextColumn::make('street_address')->label('Улица')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Создать'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Изменить'),
                Tables\Actions\DeleteAction::make()->label('Удалить'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Удалить'),
                ]),
            ]);
    }
}
