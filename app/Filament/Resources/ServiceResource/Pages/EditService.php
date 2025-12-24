<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('duplicate')
    ->label('Duplicar Serviço')
    ->icon('heroicon-o-document-duplicate')
    ->color('info')
    ->action(function () {
        $newService = $this->record->replicate();

        $newService->name = $this->record->name . ' (Cópia)';
        $newService->save();

        Notification::make()
            ->title('Serviço duplicado com sucesso!')
            ->body("Novo serviço criado: {$newService->name}")
            ->success()
            ->send();

        return redirect()->to(
            ServiceResource::getUrl('edit', ['record' => $newService])
        );
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
        return 'Serviço atualizado com sucesso!';
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Garantir que o preço seja um decimal válido
        if (isset($data['price'])) {
            $data['price'] = (float) $data['price'];
        }
        
        return $data;
    }
}
