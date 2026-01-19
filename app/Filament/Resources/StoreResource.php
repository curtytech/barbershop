<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Lojas';

    public static function getLabel(): string
    {
        return 'Loja';
    }

    public static function getPluralLabel(): string
    {
        return 'Lojas';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->role === 'store') {
            return $query->where('user_id', $user->id);
        }

        if ($user->role === 'employee') {
            return $query->whereHas('employees', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name', fn(Builder $query) => $query->where('role', 'store'))
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('celphone')
                    ->label('Celular')
                    ->tel()
                    ->mask('(99) 99999-9999')
                    ->placeholder('(21) 98765-4321')
                    ->maxLength(15),

                Forms\Components\FileUpload::make('image_logo')
                    ->label('Logo')
                    ->image(),
                Forms\Components\FileUpload::make('image_banner')
                    ->label('Banner')
                    ->image(),
                Forms\Components\ColorPicker::make('color_primary')->label('Cor Primária'),
                Forms\Components\ColorPicker::make('color_secondary')->label('Cor Secundária'),
                Forms\Components\TextInput::make('zipcode')
                    ->label('CEP')
                    ->tel()
                    ->mask('99999-999')
                    ->placeholder('12345-678')
                    ->dehydrateStateUsing(fn ($state) => preg_replace('/\D/', '', $state))
                    ->maxLength(9),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->label('Endereço'),
                Forms\Components\TextInput::make('number')
                    ->label('Número')
                    ->tel()
                    ->mask('9999999999') 
                    ->placeholder('123')
                    ->dehydrateStateUsing(fn ($state) => preg_replace('/\D/', '', $state))
                    ->maxLength(10),
                Forms\Components\TextInput::make('neighborhood')
                    ->maxLength(255)
                    ->label('Bairro'),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255)
                    ->label('Cidade'),
                Forms\Components\TextInput::make('state')
                    ->maxLength(255)
                    ->label('Estado'),
                Forms\Components\TextInput::make('complement')
                    ->maxLength(255)
                    ->label('Complemento'),
                Forms\Components\TextInput::make('instagram')
                    ->maxLength(255),
                Forms\Components\TextInput::make('facebook')
                    ->maxLength(255),
                Forms\Components\TextInput::make('whatsapp')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('celphone')
                    ->label('Celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}