<?php

namespace App\Filament\Resources\ControlPrenatalResource\Pages;

use App\Filament\Resources\ControlPrenatalResource;
use App\Models\ControlPrenatal;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewControlPrenatal extends ViewRecord
{
    protected static string $resource = ControlPrenatalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->before(function ($record){
                    $i = 0;
                    do{
                        $repeat = Str::repeat('*',++$i);
                        $string = $repeat . $record->codigo;
                        $existe = Controlprenatal::withTrashed()->where('codigo', $string)->first();
                    }while($existe);
                    $record->update(['codigo' => $string]);
                }),
        ];
    }
}
