<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enum\ClientStatus;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\HtmlString;

class ClientResource extends Resource
{
    use \App\Helpers\HasPermissionPolicy;
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationLabel(): string
    {
        return __('message.clients');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.clients');
    }

    public static function getModelLabel(): string
    {
        return __('message.client');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('message.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('company_name')
                    ->label(__('message.company_name'))
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label(__('message.email'))
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('phone')
                    ->label(__('message.phone'))
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label(__('message.status'))
                    ->options([
                        ClientStatus::INTERESTED->value => __('message.interested'),
                        ClientStatus::NEGOTIATING->value => __('message.negotiating'),
                        ClientStatus::ACTIVE->value => __('message.active'),
                        ClientStatus::FINISHED->value => __('message.finished'),
                        ClientStatus::PAUSED->value => __('message.paused'),
                    ])
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('source')
                    ->label(__('message.source'))
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label(__('message.notes'))
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('files')
                    ->label(__('message.Files'))
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label(__('message.file'))
                            ->directory('clients')
                            ->required(),
                        Forms\Components\Hidden::make('file_type')
                            ->default(Client::class),
                        Forms\Components\Hidden::make('uploaded_by')
                            ->default(fn() => auth('web')->id()),
                    ])

                    ->required()
                    ->visibleOn('create')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('created_by')
                    ->default(fn() => auth('web')->id()),
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('message.name'))
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('message.company_name'))
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('message.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('message.phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('message.status'))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        ClientStatus::INTERESTED => 'info',
                        ClientStatus::NEGOTIATING => 'warning',
                        ClientStatus::ACTIVE => 'success',
                        ClientStatus::FINISHED => 'primary',
                        ClientStatus::PAUSED => 'secondary',
                        default => 'danger',
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            ClientStatus::INTERESTED => __('message.interested'),
                            ClientStatus::NEGOTIATING => __('message.negotiating'),
                            ClientStatus::ACTIVE => __('message.active'),
                            ClientStatus::FINISHED => __('message.finished'),
                            ClientStatus::PAUSED => __('message.paused'),
                            default => 'danger',
                        };
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->label(__('message.source'))
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('message.created_by'))
                    ->limit(50)
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('show_files')
                    ->label(__('message.show_files'))
                    ->visible(fn($record) => $record->files && $record->files->count() > 0)
                    ->icon('heroicon-o-paper-clip')
                    ->color('gray')
                    ->modalHeading(__('message.attached_files'))
                    ->modalCancelActionLabel(__('message.close'))
                    ->modalContent(function ($record) {
                        $html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2">';

                        foreach ($record->files as $file) {
                            $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                            $url = asset('storage/' . $file->file_path);
                            $name = basename($file->file_path);

                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico', 'bmp', 'tiff']);

                            $icon = match (true) {
                                in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico', 'bmp', 'tiff']) => '🖼️',
                                in_array($ext, ['mp4', 'webm']) => '🎥',
                                in_array($ext, ['mp3', 'wav', 'ogg']) => '🎵',
                                $ext === 'pdf' => '📄',
                                default => '📁',
                            };

                            $html .= "<div class='border rounded p-3 shadow-sm bg-white'>
                            <div class='mb-2 text-sm font-medium'>
                                <a href='$url' target='_blank' class='text-blue-600 hover:underline'>$icon $name</a>
                            </div>";

                            if ($isImage) {
                                $html .= "<img src='$url' class='w-full max-h-48 object-contain rounded border'>";
                            }

                            $html .= "</div>";
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\FilesRelationManager::class,
            RelationManagers\InternalNotesRelationManager::class,
            RelationManagers\RemindersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
