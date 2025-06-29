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

class RemindersRelationManager extends RelationManager
{
    protected static string $relationship = 'reminders';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('message.reminders');
    }
    public static function getNavigationLabel(): string
    {
        return __('message.reminders');
    }

    public static function getModelLabel(): string
    {
        return __('message.reminder');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.reminders');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('message.title'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('remind_at')
                    ->label(__('message.remind_at'))
                    ->required(),
                Forms\Components\Hidden::make('created_by')
                    ->default(auth('web')->id())

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('message.title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remind_at')
                    ->label(__('message.remind_at'))
                    ->dateTime('Y-m-d h:i A')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BooleanColumn::make('notified')
                    ->label(__('message.notified'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('message.created_at'))
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
