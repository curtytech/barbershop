<?php

namespace App\Filament\Resources\AppointmentTimeResource\Pages;

use App\Filament\Resources\AppointmentTimeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAppointmentTime extends CreateRecord
{
    protected static string $resource = AppointmentTimeResource::class;

    public function getTitle(): string
    {
        return 'Criar Horário de Atendimento';
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
}