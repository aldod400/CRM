<?php

namespace App\Filament\Resources;

use App\Enum\ProjectStatus;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ProjectResource extends Resource
{
    use \App\Helpers\HasPermissionPolicy;
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    public static function getNavigationLabel(): string
    {
        return __('message.projects');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.projects');
    }

    public static function getModelLabel(): string
    {
        return __('message.project');
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
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('message.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('message.description'))
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('message.start_date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('message.end_date')),
                Forms\Components\Select::make('status')
                    ->label(__('message.status'))
                    ->options([
                        ProjectStatus::PENDING->value => __('message.pending'),
                        ProjectStatus::INPROGRESS->value => __('message.in_progress'),
                        ProjectStatus::COMPLETED->value => __('message.completed'),
                        ProjectStatus::DELAYED->value => __('message.delayed'),
                    ])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('assigned_to')
                    ->label(__('message.assigned_to'))
                    ->schema([
                        Forms\Components\Select::make('id')
                            ->label(__('message.user'))
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('pivot.role')
                            ->label(__('message.role'))
                            ->options([
                                'developer' => __('message.developer'),
                                'manager' => __('message.manager'),
                                'designer' => __('message.designer'),
                                'member'  => __('message.member')
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('pivot.joined_at')
                            ->label(__('message.joined_at'))
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('pivot.is_active')
                            ->label(__('message.is_active'))
                            ->default(true),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->reorderable(false)
                    ->cloneable(false)
                    ->columnSpanFull()
                    ->visibleOn('create'),
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
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('message.client'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('message.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('message.start_date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('message.end_date'))
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('message.status'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            ProjectStatus::PENDING => __('message.pending'),
                            ProjectStatus::INPROGRESS => __('message.in_progress'),
                            ProjectStatus::COMPLETED => __('message.completed'),
                            ProjectStatus::DELAYED => __('message.delayed'),
                            default => $state,
                        };
                    })
                    ->color(fn($state) => match ($state) {
                        ProjectStatus::PENDING => 'warning',
                        ProjectStatus::INPROGRESS => 'info',
                        ProjectStatus::COMPLETED => 'success',
                        ProjectStatus::DELAYED => 'danger',
                        default => 'secondary',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('message.created_by'))
                    ->numeric()
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
            RelationManagers\AssignedToRelationManager::class,
            RelationManagers\FilesRelationManager::class,
            RelationManagers\InternalNotesRelationManager::class,
            RelationManagers\RemindersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
