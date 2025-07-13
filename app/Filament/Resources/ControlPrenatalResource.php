<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControlprenatalResource\Pages;
use App\Filament\Resources\ControlprenatalResource\RelationManagers;
use App\Filament\Resources\ControlPrenatalResource\Widgets\AntecedentesFamiliaresWidget;
use App\Livewire\AntecedentesFamiliaresComponent;
use App\Livewire\AntecedentesPersonalesComponent;
use App\Models\Controlprenatal;
use App\Models\Paciente;
use App\Models\Parametro;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ControlPrenatalResource extends Resource
{
    protected static ?string $model = Controlprenatal::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?string $navigationLabel = 'Control Prenatal';
    protected static ?string $label = 'Control Prenatal';
    protected static ?string $pluralLabel = 'Control Prenatal';
    protected static ?string $slug = 'control-prenatal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos Básicos')
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 3
                        ])
                            ->schema([
                                Forms\Components\TextInput::make('codigo')
                                    ->unique(ignoreRecord: true)
                                    ->default(function (): string {
                                        $parametro = Parametro::where('nombre', 'codigo_control_prenatal')->first();
                                        if ($parametro) {
                                            $num = $parametro->valor_id ? $parametro->valor_id : 1;
                                            $formato = mb_strtoupper($parametro->valor_texto);
                                            $codigo = $formato . cerosIzquierda($num, numSizeCodigo());
                                        } else {
                                            $codigo = cerosIzquierda(1, numSizeCodigo());
                                        }
                                        return "$codigo";
                                    })
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('pacientes_id')
                                    ->relationship('paciente', 'nombre')
                                    ->getOptionLabelFromRecordUsing(fn(Paciente $record) => mb_strtoupper("{$record->nombre}"))
                                    ->searchable(['cedula', 'nombre'])
                                    ->preload()
                                    ->required()
                                    ->columnSpan(['sm' => 2])
                                    ->createOptionForm([
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
                                            ->columns()
                                            ->compact(),
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
                                            ->columns(3)
                                            ->compact(),
                                    ])
                                    ->createOptionModalHeading('Nuevo Paciente')
                                    ->rules([
                                        fn(): Closure => function (string $attribute, $value, Closure $fail) {
                                            $existe = Controlprenatal::where('pacientes_id', $value)->first();
                                            if ($existe) {
                                                $fail('El campo paciente ya ha sido registrado.');
                                            }
                                        }
                                    ]),
                            ])
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('paciente.cedula')
                    ->label('Cédula')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('paciente.nombre')
                    ->label('Nombre y Apellido')
                    ->searchable()
                    ->formatStateUsing(fn($state) => mb_strtoupper($state))
                    ->wrap(),
                Tables\Columns\TextColumn::make('paciente.fecha_nacimiento')
                    ->label('Fecha de Nacimineto')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('paciente.edad')
                    ->label('Edad')
                    ->numeric()
                    ->alignCenter()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('paciente.telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->visibleFrom('sm'),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function ($record) {
                            $i = 0;
                            do {
                                $repeat = Str::repeat('*', ++$i);
                                $string = $repeat . $record->codigo;
                                $existe = Controlprenatal::withTrashed()->where('codigo', $string)->first();
                            } while ($existe);
                            $record->update(['codigo' => $string]);
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                $i = 0;
                                do {
                                    $repeat = Str::repeat('*', ++$i);
                                    $string = $repeat . $record->codigo;
                                    $existe = Controlprenatal::withTrashed()->where('codigo', $string)->first();
                                } while ($existe);
                                $record->update(['codigo' => $string]);
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
            'index' => Pages\ListControlPrenatals::route('/'),
            'create' => Pages\CreateControlPrenatal::route('/create'),
            'view' => Pages\ViewControlPrenatal::route('/{record}'),
            'edit' => Pages\EditControlPrenatal::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Section::make()
                        ->schema([
                            TextEntry::make('codigo')
                                ->label('Código')
                                ->weight(FontWeight::Bold)
                                ->copyable()
                                ->color('primary'),
                        ])
                        ->grow(false),
                    Section::make('Paciente')
                        ->schema([
                            Fieldset::make('Datos Básicos')
                                ->schema([
                                    TextEntry::make('paciente.cedula')
                                        ->label('Cédula')
                                        ->numeric()
                                        ->weight(FontWeight::Bold)
                                        ->copyable()
                                        ->color('primary'),
                                    TextEntry::make('paciente.nombre')
                                        ->label('Nombre y Apellido')
                                        ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                        ->weight(FontWeight::Bold)
                                        ->copyable()
                                        ->color('primary'),
                                    TextEntry::make('paciente.edad')
                                        ->label('Edad')
                                        ->numeric()
                                        ->weight(FontWeight::Bold)
                                        ->copyable()
                                        ->color('primary'),
                                    TextEntry::make('paciente.telefono')
                                        ->label('Teléfono')
                                        ->weight(FontWeight::Bold)
                                        ->copyable()
                                        ->color('primary'),
                                    TextEntry::make('paciente.direccion')
                                        ->label('Dirección')
                                        ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                        ->weight(FontWeight::Bold)
                                        ->copyable()
                                        ->color('primary')
                                        ->columnSpan(2),
                                ])
                                ->columns(3),
                            Fieldset::make('Datos Médicos')
                                ->schema([
                                    Grid::make([
                                        'default' => 2,
                                        'sm' => 3,
                                    ])
                                        ->schema([
                                            TextEntry::make('paciente.fur')
                                                ->label('FUR')
                                                ->date()
                                                ->weight(FontWeight::Bold)
                                                ->copyable()
                                                ->color('primary'),
                                            TextEntry::make('paciente.fpp')
                                                ->label('FPP')
                                                ->date()
                                                ->weight(FontWeight::Bold)
                                                ->copyable()
                                                ->color('primary'),
                                            TextEntry::make('paciente.gestas')
                                                ->label('Gestas')
                                                ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                                ->weight(FontWeight::Bold)
                                                ->copyable()
                                                ->color('primary'),
                                            TextEntry::make('paciente.partos')
                                                ->label('partos')
                                                ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                                ->weight(FontWeight::Bold)
                                                ->copyable()
                                                ->color('primary'),
                                            TextEntry::make('paciente.cesareas')
                                                ->label('Cesareas')
                                                ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                                ->weight(FontWeight::Bold)
                                                ->copyable()
                                                ->color('primary'),
                                            TextEntry::make('paciente.abortos')
                                                ->label('Abortos')
                                                ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                                ->weight(FontWeight::Bold)
                                                ->copyable()
                                                ->color('primary'),
                                        ]),
                                ])
                        ])
                        ->compact()
                        ->collapsible()
                ])
                    ->from('sm')
                    ->columnSpanFull(),
                Section::make('Antecedentes')
                    ->schema([
                        Split::make([
                            Livewire::make(AntecedentesFamiliaresComponent::class),
                            Livewire::make(AntecedentesPersonalesComponent::class)
                        ])
                            ->from('sm')
                            ->columnSpanFull()
                    ])
                    ->collapsible()
                    ->compact()
            ]);
    }

}
