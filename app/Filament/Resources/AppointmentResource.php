<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Employee;
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->role === 'store') {
            $storeIds = $user->stores()->pluck('id');
            return $query->whereHas('employee', function ($q) use ($storeIds) {
                $q->whereIn('store_id', $storeIds);
            });
        }

        if ($user->role === 'employee') {
            $employeeIds = $user->employees()->pluck('id');
            return $query->whereIn('employee_id', $employeeIds);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Agendamento')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Profissional')
                            ->options(function () {
                                $user = auth()->user();
                                if ($user->role === 'employee') {
                                    return $user->employees()->pluck('name', 'id');
                                }
                                if ($user->role === 'store') {
                                    return Employee::whereIn('store_id', $user->stores()->pluck('id'))->pluck('name', 'id');
                                }
                                return Employee::all()->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('service_id', null)),
                            
                        Forms\Components\Select::make('service_id')
                            ->label('Serviço')
                            ->options(fn (Forms\Get $get) => Service::where('employee_id', $get('employee_id'))->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('appointment_time', null)),
                            
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nome do Cliente')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('client_phone')
                            ->label('Telefone do Cliente')
                            ->required()
                            ->tel()
                            ->maxLength(20)
                            ->extraInputAttributes([
                                'oninput' => "this.value = this.value
                                    .replace(/\D/g,'')
                                    .replace(/^(\d{2})(\d)/g,'($1) $2')
                                    .replace(/(\d{4,5})(\d{4})$/,'$1-$2');"
                            ]),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Data e Horário')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Data do Agendamento')
                            ->required()
                            ->minDate(now())
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('appointment_time', null)),
                            
                        Forms\Components\Select::make('appointment_time')
                            ->label('Horário')
                            ->required()
                            ->options(function (Forms\Get $get) {
                                $employeeId = $get('employee_id');
                                $serviceId = $get('service_id');
                                $date = $get('date');

                                if (!$employeeId || !$serviceId || !$date) {
                                    return [];
                                }

                                $service = Service::find($serviceId);
                                if (!$service) return [];

                                $dayOfWeek = strtolower(Carbon::parse($date)->englishDayOfWeek);
                                
                                if ($service->days_of_week && !in_array($dayOfWeek, $service->days_of_week)) {
                                    if (!$service->specific_date || $service->specific_date->format('Y-m-d') !== $date) {
                                        return [];
                                    }
                                }

                                $startTime = Carbon::parse($date . ' ' . $service->start_time->format('H:i:s'));
                                $endTime = Carbon::parse($date . ' ' . ($service->end_time ? $service->end_time->format('H:i:s') : '23:59:59'));
                                
                                $bookedTimes = Appointment::whereDate('date', $date)
                                    ->where('status', '!=', 'cancelled')
                                    ->where(function ($query) use ($service) {
                                        $query->where('employee_id', $service->employee_id)
                                              ->orWhere('service_id', $service->id);
                                    })
                                    ->get()
                                    ->map(function ($appointment) {
                                        return \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i');
                                    })
                                    ->toArray();

                                $slots = [];
                                while ($startTime->copy()->addMinutes($service->duration)->lte($endTime)) {
                                    $slotEnd = $startTime->copy()->addMinutes($service->duration);
                                    $timeStr = $startTime->format('H:i');
                                    
                                    $isBreak = false;
                                    if ($service->break_start && $service->break_end) {
                                        $breakStart = Carbon::parse($date . ' ' . $service->break_start->format('H:i:s'));
                                        $breakEnd = Carbon::parse($date . ' ' . $service->break_end->format('H:i:s'));
                                        
                                        if ($startTime->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                                            $isBreak = true;
                                        }
                                    }
                                    
                                    if (!$isBreak && !in_array($timeStr, $bookedTimes)) {
                                        $slots[$timeStr] = $timeStr;
                                    }
                                    
                                    $startTime->addMinutes($service->duration);
                                }
                                
                                return $slots;
                            })
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
                            ->extraInputAttributes([
                                'style' => 'resize: none;',
                            ])
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
                    
                Tables\Columns\TextColumn::make('employee.name')
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
                    
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Nome do Cliente')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('client_phone')
                    ->label('Telefone')
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Profissional')
                    ->options(function () {
                        $user = auth()->user();
                        if ($user->role === 'employee') {
                            return $user->employees()->pluck('name', 'id');
                        }
                        if ($user->role === 'store') {
                            return Employee::whereIn('store_id', $user->stores()->pluck('id'))->pluck('name', 'id');
                        }
                        return Employee::all()->pluck('name', 'id');
                    }),
                    
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Serviço')
                    ->options(Service::all()->pluck('name', 'id')),
                    
                Tables\Filters\Filter::make('client_name')
                    ->label('Nome do Cliente')
                    ->form([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nome do Cliente')
                            ->placeholder('Buscar por nome')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['client_name'],
                                fn (Builder $query, $name): Builder => $query->where('client_name', 'like', "%{$name}%"),
                            );
                    }),
                    
                Tables\Filters\Filter::make('client_phone')
                    ->label('Telefone do Cliente')
                    ->form([
                        Forms\Components\TextInput::make('client_phone')
                            ->label('Telefone do Cliente')
                            ->placeholder('Buscar por telefone')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['client_phone'],
                                fn (Builder $query, $phone): Builder => $query->where('client_phone', 'like', "%{$phone}%"),
                            );
                    }),
                    
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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
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