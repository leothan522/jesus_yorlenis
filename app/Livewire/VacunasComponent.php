<?php

namespace App\Livewire;

use App\Models\PacienteVacuna;
use App\Models\Vacuna;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class VacunasComponent extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Model $record;

    public function render()
    {
        return view('livewire.vacunas-component');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Vacuna::query())
            ->columns([
                Split::make([
                    TextColumn::make('nombre')
                        ->formatStateUsing(fn(string $state): string => mb_strtoupper($state)),
                    TextColumn::make('1')
                        ->default(fn($record) => $this->getVacuna($record->id)['dosis_1'])
                        ->date()
                        ->description(fn($state) => $state ? 'Dosis 1' : null, 'above')
                        ->weight(FontWeight::Bold)
                        ->color('primary')
                        ->copyable(),
                    TextColumn::make('2')
                        ->default(fn($record) => $this->getVacuna($record->id)['dosis_2'])
                        ->description(fn($state) => $state ? 'Dosis 2' : null, 'above')
                        ->date()
                        ->weight(FontWeight::Bold)
                        ->color('primary')
                        ->copyable(),
                    TextColumn::make('refuerzo')
                        ->default(fn($record) => $this->getVacuna($record->id)['refuerzo'])
                        ->date()
                        ->description(fn($state) => $state ? 'Refuerzo' : null, 'above')
                        ->weight(FontWeight::Bold)
                        ->color('primary')
                        ->copyable(),
                ])
                    ->from('sm')
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('editar_vacuna')
                    ->label(fn($record): string => mb_strtoupper($record->nombre))
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton()
                    ->form([
                        DatePicker::make('dosis_1'),
                        DatePicker::make('dosis_2'),
                        DatePicker::make('refuerzo'),
                    ])
                    ->fillForm(function ($record): array{
                        $data = [];
                        $vacuna = $this->existe($record->id);
                        if ($vacuna){
                            $data['dosis_1'] = $vacuna->dosis_1;
                            $data['dosis_2'] = $vacuna->dosis_2;
                            $data['refuerzo'] = $vacuna->refuerzo;
                        }
                        return $data;
                    })
                    ->action(function (array $data, $record):void{
                        $vacuna = $this->existe($record->id);
                        if (!$vacuna){
                            $vacuna = new PacienteVacuna();
                            $vacuna->pacientes_id = $this->record->pacientes_id;
                            $vacuna->vacunas_id = $record->id;
                        }
                        $vacuna->dosis_1 = $data['dosis_1'];
                        $vacuna->dosis_2 = $data['dosis_2'];
                        $vacuna->refuerzo = $data['refuerzo'];
                        $vacuna->save();
                    })
                    ->modalWidth(MaxWidth::Small),
            ])
            ->bulkActions([
                // ...
            ])
            ->paginated(false);
    }

    protected function existe($vacunas_id)
    {
        return PacienteVacuna::where('pacientes_id', $this->record->pacientes_id)
            ->where('vacunas_id', $vacunas_id)->first();
    }

    protected function getVacuna($vacunas_id): array
    {
        $data = [
            'dosis_1' => null,
            'dosis_2' => null,
            'refuerzo' => null,
        ];
        $vacunas = $this->existe($vacunas_id);
        if ($vacunas) {
            $data['dosis_1'] = $vacunas->dosis_1;
            $data['dosis_2'] = $vacunas->dosis_2;
            $data['refuerzo'] = $vacunas->refuerzo;
        }
        return $data;
    }

}
