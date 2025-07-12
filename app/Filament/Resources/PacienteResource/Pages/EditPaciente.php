<?php

namespace App\Filament\Resources\PacienteResource\Pages;

use App\Filament\Resources\PacienteResource;
use App\Models\Paciente;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPaciente extends EditRecord
{
    protected static string $resource = PacienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record){
                    $i = 0;
                    do{
                        $repeat = Str::repeat('*',++$i);
                        $string = $repeat . $record->cedula;
                        $existe = Paciente::withTrashed()->where('cedula', $string)->first();
                    }while($existe);
                    $record->update(['cedula' => $string]);
                }),
        ];
    }
}
