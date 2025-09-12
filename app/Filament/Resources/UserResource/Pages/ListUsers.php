<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Usuário')
                ->icon('heroicon-o-plus'),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos')
                ->badge(User::count()),
            
            'admins' => Tab::make('Administradores')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin'))
                ->badge(User::where('role', 'admin')->count()),
            
            'barbeiros' => Tab::make('Barbeiros')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'barber'))
                ->badge(User::where('role', 'barber')->count()),
            
            'usuarios' => Tab::make('Usuários')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'user'))
                ->badge(User::where('role', 'user')->count()),
            
            'verificados' => Tab::make('E-mail Verificado')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('email_verified_at'))
                ->badge(User::whereNotNull('email_verified_at')->count()),
            
            'nao_verificados' => Tab::make('E-mail Não Verificado')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('email_verified_at'))
                ->badge(User::whereNull('email_verified_at')->count()),
        ];
    }
}
