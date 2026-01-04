<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
                // =====================
                // INFORMAÇÕES PESSOAIS
                // =====================
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

                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rules(function ($record) {
                                return [
                                    Rule::unique('users', 'email')
                                        ->ignore($record ? $record->id : null),
                                ];
                            })
                            ->validationMessages([
                                'unique' => 'Este e-mail já está em uso.',
                            ]),
                    ])
                    ->columns(2),

                // =====================
                // CONFIGURAÇÕES DE ACESSO
                // =====================
                Forms\Components\Section::make('Configurações de Acesso')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Função')
                            ->options([
                                'admin' => 'Administrador',
                                'store' => 'Loja',
                                'employee' => 'Funcionário',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->same('passwordConfirmation')
                            ->validationMessages([
                                'same' => 'As senhas devem ser iguais.',
                            ])
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
                    ])
                    ->columns(2),
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
                        'warning' => 'store',
                        'success' => 'employee',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'store' => 'Loja',
                        'employee' => 'Funcionário',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('E-mail Verificado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Visualizar'),
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
            ]);
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
