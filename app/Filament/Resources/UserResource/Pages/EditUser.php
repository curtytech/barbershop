<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('verify_email')
                ->label('Verificar E-mail')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () => is_null($this->record->email_verified_at))
                ->action(function () {
                    $this->record->update([
                        'email_verified_at' => now(),
                    ]);
                    
                    Notification::make()
                        ->title('E-mail verificado com sucesso!')
                        ->success()
                        ->send();
                }),
            
            Actions\Action::make('reset_password')
                ->label('Redefinir Senha')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Redefinir Senha')
                ->modalDescription('Tem certeza que deseja redefinir a senha deste usuário? Uma nova senha será gerada.')
                ->action(function () {
                    $newPassword = '12345678'; // Senha padrão
                    $this->record->update([
                        'password' => bcrypt($newPassword),
                    ]);
                    
                    Notification::make()
                        ->title('Senha redefinida!')
                        ->body("Nova senha: {$newPassword}")
                        ->success()
                        ->persistent()
                        ->send();
                }),
            
            Actions\DeleteAction::make()
                ->label('Excluir'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Usuário atualizado com sucesso!';
    }
}
