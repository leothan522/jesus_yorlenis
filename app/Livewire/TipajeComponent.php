<?php

namespace App\Livewire;

use App\Models\PacienteTipaje;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class TipajeComponent extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    public Model $record;
    public ?PacienteTipaje $tipaje = null;
    public ?array $dataState = null;

    public function render()
    {
        $this->getTipaje();
        return view('livewire.tipaje-component');
    }

    public function tipajeInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->tipaje)
            ->state($this->dataState)
            ->schema([
                Section::make('Tipaje')
                    ->footerActions([
                        Action::make('editar_tipaje')
                            ->label('Editar Tipaje')
                            ->icon('heroicon-m-pencil-square')
                            ->iconButton()
                            ->form([
                                TextInput::make('madre'),
                                TextInput::make('padre'),
                                TextInput::make('sensibilidad'),
                            ])
                            ->fillForm(function (): array {
                                $data = [];
                                $tipaje = $this->existe();
                                if ($tipaje) {
                                    $data['madre'] = $tipaje->madre;
                                    $data['padre'] = $tipaje->padre;
                                    $data['sensibilidad'] = $tipaje->sensibilidad;
                                }
                                return $data;
                            })
                            ->action(function (array $data): void {
                                $tipaje = $this->existe();
                                if (!$tipaje) {
                                    $tipaje = new PacienteTipaje();
                                    $tipaje->pacientes_id = $this->record->pacientes_id;
                                }
                                $tipaje->madre = $data['madre'];
                                $tipaje->padre = $data['padre'];
                                $tipaje->sensibilidad = $data['sensibilidad'];
                                $tipaje->save();
                            })
                            ->modalWidth(MaxWidth::Small)
                    ])
                    ->footerActionsAlignment(Alignment::End)
                    ->schema([
                        Grid::make([
                            'default' => 2
                        ])
                            ->schema([
                                TextEntry::make('madre')
                                    ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                    ->weight(FontWeight::Bold)
                                    ->copyable()
                                    ->color('primary'),
                                TextEntry::make('padre')
                                    ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                    ->weight(FontWeight::Bold)
                                    ->copyable()
                                    ->color('primary'),
                                TextEntry::make('sensibilidad')
                                    ->formatStateUsing(fn(string $state): string => mb_strtoupper($state))
                                    ->weight(FontWeight::Bold)
                                    ->copyable()
                                    ->color('primary'),
                            ])
                    ])
                    ->collapsible(),
            ]);
    }

    protected function existe()
    {
        return PacienteTipaje::where('pacientes_id', $this->record->pacientes_id)->first();
    }

    protected function getTipaje(): void
    {
        $this->reset(['tipaje', 'dataState']);

        $state = [
            'madre' => null,
            'padre' => null,
            'sensibilidad' => null
        ];

        $tipaje = $this->existe();

        if ($tipaje) {
            $this->tipaje = $tipaje;
        } else {
            $this->dataState = $state;
        }

    }
}
