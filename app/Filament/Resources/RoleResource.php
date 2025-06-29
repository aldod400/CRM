<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Helpers\HasPermissionPolicy;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    use HasPermissionPolicy;
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('message.roles');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('message.administration');
    }

    public static function getModelLabel(): string
    {
        return __('message.role_name');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.roles');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('message.role_name'))
                            ->unique(Role::class, 'name', ignoreRecord: true),
                        Forms\Components\Select::make('permissions')
                            ->multiple()
                            ->relationship('permissions', 'name')
                            ->label(__('message.assigned_permissions'))
                            ->preload()
                            ->searchable(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('message.role_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label(__('message.assigned_permissions'))
                    ->badge()
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('message.created_at'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('message.updated_at'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('message.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('message.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('message.delete')),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
