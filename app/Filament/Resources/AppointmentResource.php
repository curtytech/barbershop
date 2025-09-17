<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Agendamentos';
    
    protected static ?string $modelLabel = 'Agendamento';
    
    protected static ?string $pluralModelLabel = 'Agendamentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Agendamento')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Profissional')
                            ->options(User::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->live(),
                            
                        Forms\Components\Select::make('service_id')
                            ->label('Serviço')
                            ->options(Service::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->live(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Data e Horário')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Data do Agendamento')
                            ->required()
                            ->minDate(now())
                            ->live(),
                            
                        Forms\Components\TimePicker::make('appointment_time')
                            ->label('Horário')
                            ->required()
                            ->seconds(false)
                            ->minutesStep(15)
                            ->helperText('Horários disponíveis de acordo com a agenda do profissional'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Observações')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'scheduled' => 'Agendado',
                                'confirmed' => 'Confirmado',
                                'completed' => 'Concluído',
                                'cancelled' => 'Cancelado',
                            ])
                            ->default('scheduled')
                            ->required(),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Informações adicionais sobre o agendamento'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Profissional')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Serviço')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Dia da Semana')
                    ->getStateUsing(function (Appointment $record): string {
                        $days = [
                            'Sunday' => 'Dom',
                            'Monday' => 'Seg',
                            'Tuesday' => 'Ter',
                            'Wednesday' => 'Qua',
                            'Thursday' => 'Qui',
                            'Friday' => 'Sex',
                            'Saturday' => 'Sáb',
                        ];
                        
                        return $days[$record->date->format('l')] ?? $record->date->format('l');
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('appointment_time')
                    ->label('Horário')
                    ->time('H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
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
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('service.formatted_price')
                    ->label('Valor')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('Observações')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Profissional')
                    ->options(User::all()->pluck('name', 'id')),
                    
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Serviço')
                    ->options(Service::all()->pluck('name', 'id')),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'scheduled' => 'Agendado',
                        'confirmed' => 'Confirmado',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                    ]),
                    
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Data de'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Data até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
                    
                Tables\Filters\Filter::make('today')
                    ->label('Hoje')
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', now())),
                    
                Tables\Filters\Filter::make('this_week')
                    ->label('Esta Semana')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('date', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])),
                    
                Tables\Filters\Filter::make('upcoming')
                    ->label('Próximos')
                    ->query(fn (Builder $query): Builder => $query->where('date', '>=', now()->toDateString())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir')
                    ->requiresConfirmation()
                    ->modalHeading('Excluir agendamento')
                    ->modalDescription('Tem certeza que deseja excluir este agendamento? Esta ação não pode ser desfeita.')
                    ->modalSubmitActionLabel('Sim, excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados')
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('date', 'asc')
            ->defaultSort('appointment_time', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('date', now())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $todayCount = static::getModel()::whereDate('date', now())->count();
        
        if ($todayCount > 10) {
            return 'danger';
        } elseif ($todayCount > 5) {
            return 'warning';
        }
        
        return 'success';
    }
}