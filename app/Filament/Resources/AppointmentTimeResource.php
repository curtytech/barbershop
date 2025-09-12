<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentTimeResource\Pages;
use App\Models\AppointmentTime;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AppointmentTimeResource extends Resource
{
    protected static ?string $model = AppointmentTime::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = 'Horários de Atendimento';
    
    protected static ?string $modelLabel = 'Horário de Atendimento';
    
    protected static ?string $pluralModelLabel = 'Horários de Atendimento';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Profissional')
                            ->options(User::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                            
                        Forms\Components\Select::make('type')
                            ->label('Tipo de Horário')
                            ->options([
                                'available' => 'Disponível',
                                'break' => 'Intervalo',
                                'lunch' => 'Almoço',
                            ])
                            ->default('available')
                            ->required()
                            ->live(),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Configuração de Tempo')
                    ->schema([
                        Forms\Components\Select::make('day_of_week')
                            ->label('Dia da Semana')
                            ->options([
                                'monday' => 'Segunda-feira',
                                'tuesday' => 'Terça-feira',
                                'wednesday' => 'Quarta-feira',
                                'thursday' => 'Quinta-feira',
                                'friday' => 'Sexta-feira',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo',
                            ])
                            ->nullable()
                            ->helperText('Deixe vazio para data específica'),
                            
                        Forms\Components\DatePicker::make('specific_date')
                            ->label('Data Específica')
                            ->nullable()
                            ->helperText('Use para horários em datas específicas'),
                            
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Horário de Início')
                            ->required()
                            ->seconds(false),
                            
                        Forms\Components\TimePicker::make('end_time')
                            ->label('Horário de Fim')
                            ->nullable()
                            ->seconds(false)
                            ->helperText('Opcional para horários sem fim definido'),
                            
                        Forms\Components\TextInput::make('duration')
                            ->label('Duração (minutos)')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'available'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Configuração de Intervalos')
                    ->schema([
                        Forms\Components\TimePicker::make('break_start')
                            ->label('Início do Intervalo')
                            ->nullable()
                            ->seconds(false)
                            ->helperText('Para intervalos dentro do horário de trabalho'),
                            
                        Forms\Components\TimePicker::make('break_end')
                            ->label('Fim do Intervalo')
                            ->nullable()
                            ->seconds(false),
                    ])
                    ->columns(2)
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['break', 'lunch'])),
                    
                Forms\Components\Section::make('Observações')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações')
                            ->nullable()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Profissional')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'available' => 'Disponível',
                        'break' => 'Intervalo',
                        'lunch' => 'Almoço',
                        default => $state
                    })
                    ->colors([
                        'success' => 'available',
                        'warning' => 'break',
                        'danger' => 'lunch',
                    ]),
                    
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Dia da Semana')
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'monday' => 'Segunda',
                        'tuesday' => 'Terça',
                        'wednesday' => 'Quarta',
                        'thursday' => 'Quinta',
                        'friday' => 'Sexta',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo',
                        default => '-'
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('specific_date')
                    ->label('Data Específica')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('formatted_time_range')
                    ->label('Horário')
                    ->getStateUsing(function (AppointmentTime $record): string {
                        $start = $record->start_time ? $record->start_time->format('H:i') : '';
                        $end = $record->end_time ? $record->end_time->format('H:i') : '';
                        
                        if ($start && $end) {
                            return "{$start} - {$end}";
                        }
                        
                        return $start ?: $end;
                    }),
                    
                Tables\Columns\TextColumn::make('formatted_break_time')
                    ->label('Intervalo')
                    ->getStateUsing(function (AppointmentTime $record): string {
                        if (!$record->break_start || !$record->break_end) {
                            return '-';
                        }
                        
                        $start = $record->break_start->format('H:i');
                        $end = $record->break_end->format('H:i');
                        
                        return "{$start} - {$end}";
                    })
                    ->visible(fn () => request()->has('tableFilters.type.value') && 
                              in_array(request('tableFilters.type.value'), ['break', 'lunch'])),
                    
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duração (min)')
                    ->sortable()
                    ->visible(fn () => !request()->has('tableFilters.type.value') || 
                              request('tableFilters.type.value') === 'available'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),
                    
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
                    
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'available' => 'Disponível',
                        'break' => 'Intervalo',
                        'lunch' => 'Almoço',
                    ]),
                    
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('Dia da Semana')
                    ->options([
                        'monday' => 'Segunda-feira',
                        'tuesday' => 'Terça-feira',
                        'wednesday' => 'Quarta-feira',
                        'thursday' => 'Quinta-feira',
                        'friday' => 'Sexta-feira',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListAppointmentTimes::route('/'),
            'create' => Pages\CreateAppointmentTime::route('/create'),
            'edit' => Pages\EditAppointmentTime::route('/{record}/edit'),
        ];
    }
}