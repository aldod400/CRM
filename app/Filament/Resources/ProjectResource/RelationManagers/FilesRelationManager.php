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

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('message.Files');
    }
    public static function getNavigationLabel(): string
    {
        return __('message.Files');
    }

    public static function getModelLabel(): string
    {
        return __('message.file');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Files');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file_path')
                    ->label(__('message.file'))
                    ->directory('projects')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->columns([
                Tables\Columns\ViewColumn::make('file_path')
                    ->label(__('message.Files'))
                    ->view('filament.tables.columns.files-list-edit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('message.created_at'))
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->searchable(),
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
