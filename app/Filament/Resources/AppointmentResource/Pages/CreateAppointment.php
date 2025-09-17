<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return 'Criar Agendamento';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Gerar uma chave única para o registro
        $data['key'] = Str::uuid()->toString();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Agendamento criado com sucesso!';
    }
}