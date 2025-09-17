<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Visualizar'),
            Actions\DeleteAction::make()
                ->label('Excluir')
                ->requiresConfirmation()
                ->modalHeading('Excluir agendamento')
                ->modalDescription('Tem certeza que deseja excluir este agendamento? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, excluir'),
        ];
    }

    public function getTitle(): string
    {
        return 'Editar Agendamento';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Agendamento atualizado com sucesso!';
    }
}