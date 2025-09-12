<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Service;
use App\Models\User;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Serviço')
                ->icon('heroicon-o-plus'),
        ];
    }
    
    public function getTabs(): array
    {
        $tabs = [
            'todos' => Tab::make('Todos os Serviços')
                ->badge(Service::count()),
        ];
        
        // Adicionar abas para cada barbeiro
        $barbers = User::where('role', 'barber')->get();
        
        foreach ($barbers as $barber) {
            $tabs['barber_' . $barber->id] = Tab::make($barber->name)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', $barber->id))
                ->badge(Service::where('user_id', $barber->id)->count());
        }
        
        // Aba para serviços sem barbeiro
        $tabs['sem_barbeiro'] = Tab::make('Sem Barbeiro')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('user_id'))
            ->badge(Service::whereNull('user_id')->count());
        
        return $tabs;
    }
}
