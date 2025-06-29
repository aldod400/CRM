<?php

namespace App\Filament\Resources;

use App\Enum\InvoiceStatus;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    use \App\Helpers\HasPermissionPolicy;
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('message.invoices');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.invoices');
    }

    public static function getModelLabel(): string
    {
        return __('message.invoice');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label(__('message.client'))
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('project_id')
                    ->label(__('message.project'))
                    ->options(function (callable $get) {
                        $clientId = $get('client_id');
                        if (!$clientId) {
                            return [];
                        }

                        return \App\Models\Project::where('client_id', $clientId)->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn(callable $get) => !$get('client_id'))
                    ->reactive(),
                Forms\Components\TextInput::make('amount')
                    ->label(__('message.amount'))
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('due_date')
                    ->label(__('message.due_date'))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__('message.status'))
                    ->options([
                        InvoiceStatus::UNPAID->value => __('message.unpaid'),
                        InvoiceStatus::PARTIALLYPAID->value => __('message.partially_paid'),
                        InvoiceStatus::PAID->value => __('message.paid'),
                    ])
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->label(__('message.notes'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('files')
                    ->label(__('message.Files'))
                    ->view('filament.tables.columns.files-list')
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('message.client'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('message.project'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('message.amount'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('message.due_date'))
                    ->date('d/m/y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('message.status'))
                    ->badge()
                    ->color(fn(InvoiceStatus $state): string => match ($state) {
                        InvoiceStatus::PAID => 'success',
                        InvoiceStatus::PARTIALLYPAID => 'warning',
                        InvoiceStatus::UNPAID => 'danger',
                    })
                    ->formatStateUsing(fn(InvoiceStatus $state): string => match ($state) {
                        InvoiceStatus::PAID => __('message.paid'),
                        InvoiceStatus::PARTIALLYPAID => __('message.partially_paid'),
                        InvoiceStatus::UNPAID => __('message.unpaid'),
                    })
                    ->searchable()
                    ->sortable(),
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
            RelationManagers\FilesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
