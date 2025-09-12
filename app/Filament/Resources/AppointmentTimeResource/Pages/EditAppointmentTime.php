<?php

namespace App\Filament\Resources\AppointmentTimeResource\Pages;

use App\Filament\Resources\AppointmentTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointmentTime extends EditRecord
{
    protected static string $resource = AppointmentTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir'),
        ];
    }

    public function getTitle(): string
    {
        return 'Editar Horário de Atendimento';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}