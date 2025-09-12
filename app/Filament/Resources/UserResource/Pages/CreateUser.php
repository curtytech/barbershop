<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Usuário criado com sucesso!';
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Verificar email automaticamente se for admin
        if ($data['role'] === 'admin' && empty($data['email_verified_at'])) {
            $data['email_verified_at'] = now();
        }
        
        return $data;
    }
}
