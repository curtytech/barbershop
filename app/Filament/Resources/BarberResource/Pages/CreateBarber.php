<?php

namespace App\Filament\Resources\BarberResource\Pages;

use App\Filament\Resources\BarberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBarber extends CreateRecord
{
    protected static string $resource = BarberResource::class;

    public function getTitle(): string
    {
        return 'Criar Barbeiro';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Barbeiro criado com sucesso!';
    }
}