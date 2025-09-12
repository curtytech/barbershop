<?php

namespace App\Filament\Resources\AppointmentTimeResource\Pages;

use App\Filament\Resources\AppointmentTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppointmentTimes extends ListRecords
{
    protected static string $resource = AppointmentTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Horário'),
        ];
    }

    public function getTitle(): string
    {
        return 'Horários de Atendimento';
    }
}