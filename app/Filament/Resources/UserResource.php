<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Usuários';
    
    protected static ?string $modelLabel = 'Usuário';
    
    protected static ?string $pluralModelLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Pessoais')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('Usado para URLs amigáveis'),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true),
                    ])->columns(2),
                
                Forms\Components\Section::make('Configurações de Acesso')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Função')
                            ->options([
                                'admin' => 'Administrador',
                                'barber' => 'Barbeiro',
                                'user' => 'Usuário',
                            ])
                            ->required()
                            ->default('user'),
                        
                        Forms\Components\TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->same('passwordConfirmation')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                        
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->label('Confirmar Senha')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(false),
                        
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('E-mail Verificado em')
                            ->helperText('Deixe vazio se o e-mail não foi verificado'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Imagens')
                    ->schema([
                        FileUpload::make('image_logo')
                            ->label('Logo')
                            ->image()
                            ->directory('users/logos')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                                '16:9',
                            ])
                            ->maxSize(2048)
                            ->helperText('Tamanho máximo: 2MB. Formatos aceitos: JPG, PNG, GIF'),
                        
                        FileUpload::make('image_banner')
                            ->label('Banner')
                            ->image()
                            ->directory('users/banners')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '21:9',
                                '3:1',
                            ])
                            ->maxSize(5120)
                             ->helperText('Tamanho máximo: 5MB. Recomendado: formato panorâmico'),
                     ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_logo')
                    ->label('Logo')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl('/images/default-avatar.png'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Função')
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'barber',
                        'success' => 'user',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'barber' => 'Barbeiro',
                        'user' => 'Usuário',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('E-mail Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at)),
                
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
                Tables\Filters\SelectFilter::make('role')
                    ->label('Função')
                    ->options([
                        'admin' => 'Administrador',
                        'barber' => 'Barbeiro',
                        'user' => 'Usuário',
                    ]),
                
                Tables\Filters\Filter::make('email_verified')
                    ->label('E-mail Verificado')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                
                Tables\Filters\Filter::make('email_not_verified')
                    ->label('E-mail Não Verificado')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
