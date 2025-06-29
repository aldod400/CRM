<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Helpers\HasPermissionPolicy;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    use HasPermissionPolicy;
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 1;
    public static function getNavigationLabel(): string
    {
        return __('message.users');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.users');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('message.administration');
    }

    public static function getModelLabel(): string
    {
        return __('message.user');
    }

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('message.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('message.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label(__('message.phone'))
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->label(__('message.image')),
                Forms\Components\TextInput::make('password')
                    ->label(__('message.password'))
                    ->password()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrated(fn($state) => filled($state)),
                Forms\Components\Select::make('role')
                    ->label(__('message.role'))
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('message.is_active'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('message.image'))
                    ->defaultImageUrl(fn($record) => "https://ui-avatars.com/api/?name=" . urlencode($record->name ?? '') . "&background=random")
                    ->circular()
                    ->rounded()
                    ->size(30),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('message.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('message.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('message.phone'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('message.is_active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('message.role'))
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('message.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('message.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->id !== 1),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
