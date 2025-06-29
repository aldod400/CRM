<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternalNotesRelationManager extends RelationManager
{
    protected static string $relationship = 'internalNotes';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('message.Internal Notes');
    }
    public static function getNavigationLabel(): string
    {
        return __('message.Internal Notes');
    }

    public static function getModelLabel(): string
    {
        return __('message.Internal Note');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Internal Notes');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('content')
                    ->required()
                    ->maxLength(255)
                    ->label(__('message.content'))
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('created_by')
                    ->default(auth('web')->id())
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->label(__('message.content'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('message.created_by'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('message.created_at'))
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
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
