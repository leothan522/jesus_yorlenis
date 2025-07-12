<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParametroResource\Pages;
use App\Filament\Resources\ParametroResource\RelationManagers;
use App\Filament\Resources\ParametroResource\Widgets\FormatoControlPrenatal;
use App\Models\Parametro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParametroResource extends Resource
{
    protected static ?string $model = Parametro::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('valor_id')
                            ->numeric(),
                        Forms\Components\Textarea::make('valor_texto')
                            ->columnSpanFull(),
                    ])
                    ->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('valor_id')
                    ->numeric()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('valor_texto')
                    ->wrap()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('sm'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParametros::route('/'),
            'create' => Pages\CreateParametro::route('/create'),
            'edit' => Pages\EditParametro::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            FormatoControlPrenatal::class
        ];
    }
}
