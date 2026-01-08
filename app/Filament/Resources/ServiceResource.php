<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Employee;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    
    protected static ?string $navigationLabel = 'Serviços';
    
    protected static ?string $modelLabel = 'Serviço';
    
    protected static ?string $pluralModelLabel = 'Serviços';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Serviço')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('key', Str::slug($state)) : null),
                        
                        Forms\Components\Select::make('employee_id')
                            ->label('Funcionário')
                            ->relationship('employee', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Detalhes')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->required()
                            ->extraInputAttributes([
                                'style' => 'resize: none;',
                            ])
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('price')
                            ->label('Preço')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                          ->prefix('R$')
                            ->helperText('Digite o valor em reais (ex: 50,00)')
                            ->placeholder('0,00'),
                        
                        FileUpload::make('image')
                            ->label('Imagem')
                            ->image()
                            ->directory('services')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->helperText('Tamanho máximo: 2MB'),
                    ])->columns(2),

                Forms\Components\Section::make('Horários / Configuração')
                    ->schema([
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Início')
                            ->required(),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('Fim'),

                        Forms\Components\Select::make('day_of_week')
                            ->label('Dia da semana')
                            ->options([
                                'monday' => 'Segunda-feira',
                                'tuesday' => 'Terça-feira',
                                'wednesday' => 'Quarta-feira',
                                'thursday' => 'Quinta-feira',
                                'friday' => 'Sexta-feira',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo',
                            ])
                            ->searchable(),

                        Forms\Components\DatePicker::make('specific_date')
                            ->label('Data específica'),

                        Forms\Components\TextInput::make('duration')
                            ->label('Duração (min)')
                            ->numeric()
                            ->minValue(1)
                            ->default(30)
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'available' => 'Disponível',
                                'break' => 'Pausa',
                                'lunch' => 'Almoço',
                            ])
                            ->default('available')
                            ->required(),

                        Forms\Components\TimePicker::make('break_start')
                            ->label('Início da pausa'),

                        Forms\Components\TimePicker::make('break_end')
                            ->label('Fim da pausa'),
                    ])->columns(2),
            ]);
    }
    


   
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Imagem')
                    ->circular()
                    ->size(60),
                                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Funcionário')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.'))
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Disponível',
                        'break' => 'Pausa',
                        'lunch' => 'Almoço',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'break' => 'warning',
                        'lunch' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Dia da semana')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'monday' => 'Segunda',
                        'tuesday' => 'Terça',
                        'wednesday' => 'Quarta',
                        'thursday' => 'Quinta',
                        'friday' => 'Sexta',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('specific_date')
                    ->label('Data específica')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Início')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fim')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duração (min)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Funcionário')
                    ->options(Employee::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('price_range')
                    ->label('Faixa de Preço')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Preço mínimo (R$)')
                            ->numeric()
                            ->placeholder('0,00'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Preço máximo (R$)')
                            ->numeric()
                            ->placeholder('100,00'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}