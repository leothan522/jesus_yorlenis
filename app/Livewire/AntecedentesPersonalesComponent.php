<?php

namespace App\Livewire;

use App\Models\AntecedentesPersonal;
use App\Models\PacienteAntPersonal;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class AntecedentesPersonalesComponent extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Model $record;

    public function render()
    {
        return view('livewire.antecedentes-personales-component');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AntecedentesPersonal::query()
            )
            ->columns([
                TextColumn::make('nombre')
                    ->label('Antecedentes Personales')
                    ->description(function ($record): string {
                        $response = '';
                        $antecedente = $this->existe($record->id);
                        if ($antecedente && !empty($antecedente->texto)) {
                            $response = mb_strtoupper($antecedente->texto);
                        }
                        return $response;
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('is_bool')
                    ->label('Seleccionar')
                    ->iconButton()
                    ->icon(fn($record): string => $this->getIcono($record->id))
                    ->hidden(fn($record): bool => !$record->is_bool)
                    ->action(function ($record): void {
                        $antecedente = $this->existe($record->id);
                        if ($antecedente) {
                            $antecedente->delete();
                        } else {
                            $this->crearAntecedente($record->id);
                        }
                    }),
                Action::make('is_texto')
                    ->label(fn($record): string => $this->existe($record->id) ? 'Editar' : 'Seleccionar')
                    ->iconButton()
                    ->icon(fn($record): string => $this->getIcono($record->id, true))
                    ->hidden(fn($record): bool => $record->is_bool)
                    ->form([
                        TextInput::make('texto')
                            ->label('Especifique'),
                    ])
                    ->fillForm(function ($record): array {
                        $data = [];
                        $antecedente = $this->existe($record->id);
                        if ($antecedente) {
                            $data['texto'] = $antecedente->texto;
                        }
                        return $data;
                    })
                    ->modalWidth(MaxWidth::Small)
                    ->modalHeading('Antecedente Personal (Otro)')
                    ->action(function (array $data, $record): void {
                        $antecedente = $this->existe($record->id);
                        if ($antecedente) {
                            if (!empty($data['texto'])) {
                                $antecedente->texto = $data['texto'];
                                $antecedente->save();
                            } else {
                                $antecedente->delete();
                            }
                        } else {
                            $this->crearAntecedente($record->id, $data['texto']);
                        }
                    }),
            ])
            ->bulkActions([
                // ...
            ])
            ->paginated(false);
    }

    protected function existe($antecedentes_id)
    {
        return PacienteAntPersonal::where('pacientes_id', $this->record->pacientes_id)
            ->where('antecedentes_id', $antecedentes_id)
            ->first();
    }

    protected function crearAntecedente($antecedentes_id, $texto = null): void
    {
        $antecedente = new PacienteAntPersonal();
        $antecedente->pacientes_id = $this->record->pacientes_id;
        $antecedente->antecedentes_id = $antecedentes_id;
        if (!empty($texto)) {
            $antecedente->texto = $texto;
        }
        $antecedente->save();
    }

    protected function getIcono($antecedentes_id, $otro = false): string
    {
        if ($this->existe($antecedentes_id)) {
            $icon = 'heroicon-m-check-circle';
            if ($otro) {
                $icon = 'heroicon-m-pencil-square';
            }
        } else {
            $icon = 'heroicon-o-stop';
        }
        return $icon;
    }
}
