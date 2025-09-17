<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Agendamento'),
        ];
    }

    public function getTitle(): string
    {
        return 'Agendamentos';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->badge(fn () => $this->getModel()::count()),
                
            'scheduled' => Tab::make('Agendados')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'scheduled'))
                ->badge(fn () => $this->getModel()::where('status', 'scheduled')->count()),
                
            'confirmed' => Tab::make('Confirmados')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed'))
                ->badge(fn () => $this->getModel()::where('status', 'confirmed')->count()),
                
            'today' => Tab::make('Hoje')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('date', today()))
                ->badge(fn () => $this->getModel()::whereDate('date', today())->count()),
                
            'upcoming' => Tab::make('Próximos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('date', '>=', today())->whereIn('status', ['scheduled', 'confirmed']))
                ->badge(fn () => $this->getModel()::where('date', '>=', today())->whereIn('status', ['scheduled', 'confirmed'])->count()),
                
            'completed' => Tab::make('Concluídos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(fn () => $this->getModel()::where('status', 'completed')->count()),
        ];
    }
}