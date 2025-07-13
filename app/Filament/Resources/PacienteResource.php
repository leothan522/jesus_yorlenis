<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacienteResource\Pages;
use App\Filament\Resources\PacienteResource\RelationManagers;
use App\Models\Paciente;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PacienteResource extends Resource
{
    protected static ?string $model = Paciente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos Básicos')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 3,
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('cedula')
                                    ->label('Cédula')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->maxLength(15),
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre y Apellido')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(['sm' => 2]),
                            ]),
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 3,
                        ])
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_nacimiento')
                                    ->label('Fecha de Nacimiento')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        if (!empty($get('fecha_nacimiento'))) {
                                            $edad = Carbon::create($get('fecha_nacimiento'))->age;
                                            $set('edad', $edad);
                                        }
                                    }),
                                Forms\Components\TextInput::make('edad')
                                    ->numeric()
                                    ->readOnly(fn(Get $get) => !empty($get('fecha_nacimiento')))
                                    ->minValue(0)
                                ->requiredWithout('fecha_nacimiento'),
                                Forms\Components\TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                                Forms\Components\Textarea::make('direccion')
                                    ->label('Dirección')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columns(),
                Forms\Components\Section::make('Datos Médicos')
                    ->schema([
                        Forms\Components\DatePicker::make('fur')
                            ->label('FUR'),
                        Forms\Components\DatePicker::make('fpp')
                            ->label('FPP'),
                        Forms\Components\TextInput::make('gestas')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('partos')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('cesareas')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('abortos')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cedula')
                    ->label('Cédula')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre y Apellido')
                    ->searchable()
                    ->formatStateUsing(fn($state) => mb_strtoupper($state))
                    ->wrap(),
                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->label('Fecha de Nacimineto')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('edad')
                    ->numeric()
                    ->alignCenter()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('sm'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function ($record){
                            $i = 0;
                            do{
                                $repeat = Str::repeat('*',++$i);
                                $string = $repeat . $record->cedula;
                                $existe = Paciente::withTrashed()->where('cedula', $string)->first();
                            }while($existe);
                            $record->update(['cedula' => $string]);
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records){
                            foreach ($records as $record){
                                $i = 0;
                                do{
                                    $repeat = Str::repeat('*',++$i);
                                    $string = $repeat . $record->cedula;
                                    $existe = Paciente::withTrashed()->where('cedula', $string)->first();
                                }while($existe);
                                $record->update(['cedula' => $string]);
                            }
                        }),
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
            'index' => Pages\ListPacientes::route('/'),
            'create' => Pages\CreatePaciente::route('/create'),
            'edit' => Pages\EditPaciente::route('/{record}/edit'),
            'view' => Pages\ViewPaciente::route('/{record}'),
        ];
    }
}
