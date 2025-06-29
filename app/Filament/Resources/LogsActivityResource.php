<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogsActivityResource\Pages;
use App\Filament\Resources\LogsActivityResource\RelationManagers;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Activitylog\Models\Activity;

class LogsActivityResource extends Resource
{
    use \App\Helpers\HasPermissionPolicy;

    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 4;
    public static function getNavigationLabel(): string
    {
        return __('message.logs_activities');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.logs_activities');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('message.administration');
    }

    public static function getModelLabel(): string
    {
        return __('message.logs_activity');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('message.description'))
                    ->wrap(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label(__('message.created_by'))
                    ->default('System')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->getStateUsing(fn($record) => class_basename($record->subject_type))
                    ->label(__('message.subject_type')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('message.created_at'))
                    ->dateTime('Y-m-d h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ListLogsActivities::route('/'),
        ];
    }
}
