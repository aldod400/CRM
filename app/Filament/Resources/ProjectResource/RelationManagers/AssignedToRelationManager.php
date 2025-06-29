<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssignedToRelationManager extends RelationManager
{
    protected static string $relationship = 'projectUsers';
    public static function getNavigationLabel(): string
    {
        return __('message.assigned_to');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.assigned_to');
    }

    public static function getModelLabel(): string
    {
        return __('message.assigned_to');
    }
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('message.assigned_to');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('message.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('role')
                    ->label(__('message.role'))
                    ->options([
                        'developer' => __('message.developer'),
                        'manager' => __('message.manager'),
                        'designer' => __('message.designer'),
                        'member'  => __('message.member')
                    ])
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('joined_at')
                    ->label(__('message.joined_at'))
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('message.is_active'))
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('role')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('message.user'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label(__('message.role'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (string $state) {
                        return match ($state) {
                            'developer' => __('message.developer'),
                            'manager' => __('message.manager'),
                            'designer' => __('message.designer'),
                            'member' => __('message.member'),
                            default => $state,
                        };
                    }),
                Tables\Columns\TextColumn::make('joined_at')
                    ->label(__('message.joined_at'))
                    ->searchable()
                    ->sortable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('is_active')
                    ->label(__('message.status'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn(string $state): string => $state ? __('message.active') : __('message.inactive')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
