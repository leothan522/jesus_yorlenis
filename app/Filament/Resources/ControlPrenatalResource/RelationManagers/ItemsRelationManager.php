<?php

namespace App\Filament\Resources\ControlPrenatalResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Control';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('fecha')
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule, RelationManager $livewire) {
                                    return $rule->where('control_id', $livewire->getOwnerRecord()->id);
                                }
                            ),
                        Forms\Components\TextInput::make('edad_gestacional')
                            ->label('Edad Gestacional')
                            ->integer(),
                        Forms\Components\TextInput::make('peso')
                            ->numeric(),
                        Forms\Components\TextInput::make('ta')
                            ->label('Tensión Arterial')
                            ->integer(),
                        Forms\Components\TextInput::make('au')
                            ->label('Altura Uterina')
                            ->integer(),
                        Forms\Components\TextInput::make('pres')
                            ->label('Presentación'),
                        Forms\Components\Toggle::make('mov_fetales')
                            ->label('Mov. Fetales'),
                        Forms\Components\Toggle::make('du')
                            ->label('Dinámica Uterina'),
                        Forms\Components\Toggle::make('edema'),
                        Forms\Components\TextInput::make('fcf')
                            ->label('Frecuencia Cardíaca Fetal')
                            ->integer(),
                        Forms\Components\TextInput::make('sintomas'),
                        Forms\Components\TextInput::make('observaciones'),
                    ])
                    ->columns(3)
                    ->compact(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fecha')
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->grow(false),
                Tables\Columns\TextColumn::make('edad_gestacional')
                    ->label('Edad Gest.')
                    ->numeric()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('peso')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('ta')
                    ->label('TA')
                    ->numeric()
                    ->alignCenter()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('au')
                    ->label('AU')
                    ->numeric()
                    ->alignCenter()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('pres')
                    ->label('PRES')
                    ->words(2)
                    ->visibleFrom('sm')
                    ->formatStateUsing(fn(string $state): string => mb_strtoupper($state)),
                Tables\Columns\TextColumn::make('fcf')
                    ->label('FCF')
                    ->numeric()
                    ->alignEnd()
                    ->visibleFrom('sm'),
                Tables\Columns\IconColumn::make('mov_fetales')
                    ->label('Mov. Fetales')
                    ->boolean()
                    ->alignCenter()
                    ->visibleFrom('sm'),
                Tables\Columns\IconColumn::make('du')
                    ->label('DU')
                    ->boolean()
                    ->alignCenter()
                    ->visibleFrom('sm'),
                Tables\Columns\IconColumn::make('edema')
                    ->boolean()
                    ->alignCenter()
                    ->visibleFrom('sm'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Control')
                    ->createAnother(false)
                    ->modalHeading('Agregar Control'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalHeading(fn($record): string => 'Control ' . getFecha($record->fecha)),
                    Tables\Actions\DeleteAction::make()
                        ->modalHeading(fn($record): string => 'Borrar ' . getFecha($record->fecha)),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalHeading('Borrar seleccionados'),
                ]),
            ])
            ->emptyStateDescription('Agregue un control para empezar.')
            ->modifyQueryUsing(fn(Builder $query) => $query->orderBy('fecha', 'desc'));
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
