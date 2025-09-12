<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Serviço criado com sucesso!';
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Garantir que o preço seja um decimal válido
        if (isset($data['price'])) {
            $data['price'] = (float) $data['price'];
        }
        
        return $data;
    }
}
