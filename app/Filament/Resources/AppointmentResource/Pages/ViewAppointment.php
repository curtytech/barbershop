<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),
            Actions\DeleteAction::make()
                ->label('Excluir')
                ->requiresConfirmation(),
        ];
    }

    public function getTitle(): string
    {
        return 'Visualizar Agendamento';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informações do Agendamento')
                    ->schema([
                        TextEntry::make('key')
                            ->label('ID do Agendamento'),
                            
                        TextEntry::make('user.name')
                            ->label('Profissional'),
                            
                        TextEntry::make('service.name')
                            ->label('Serviço'),
                            
                        TextEntry::make('service.formatted_price')
                            ->label('Valor do Serviço'),
                    ])
                    ->columns(2),
                    
                Section::make('Data e Horário')
                    ->schema([
                        TextEntry::make('formatted_date')
                            ->label('Data')
                            ->getStateUsing(fn ($record) => $record->date->format('d/m/Y')),
                            
                        TextEntry::make('day_of_week')
                            ->label('Dia da Semana')
                            ->getStateUsing(function ($record): string {
                                $days = [
                                    'Sunday' => 'Domingo',
                                    'Monday' => 'Segunda-feira',
                                    'Tuesday' => 'Terça-feira',
                                    'Wednesday' => 'Quarta-feira',
                                    'Thursday' => 'Quinta-feira',
                                    'Friday' => 'Sexta-feira',
                                    'Saturday' => 'Sábado',
                                ];
                                
                                return $days[$record->date->format('l')] ?? $record->date->format('l');
                            }),
                            
                        TextEntry::make('formatted_time')
                            ->label('Horário')
                            ->getStateUsing(fn ($record) => $record->appointment_time->format('H:i')),
                            
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'scheduled' => 'Agendado',
                                'confirmed' => 'Confirmado',
                                'completed' => 'Concluído',
                                'cancelled' => 'Cancelado',
                                default => 'Desconhecido',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'scheduled' => 'warning',
                                'confirmed' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),
                    
                Section::make('Observações')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Observações')
                            ->placeholder('Nenhuma observação registrada')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->notes)),
                    
                Section::make('Informações do Sistema')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i:s'),
                            
                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}