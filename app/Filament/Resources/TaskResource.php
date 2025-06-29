<?php

namespace App\Filament\Resources;

use App\Enum\TaskStatus;
use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    public static function getNavigationLabel(): string
    {
        return __('message.tasks');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.tasks');
    }

    public static function getModelLabel(): string
    {
        return __('message.task');
    }
    public static function getEloquentQuery(): Builder
    {
        if (
            auth('web')->user()?->can('create tasks')
            || auth('web')->user()?->can('edit tasks')
            || auth('web')->user()?->can('delete tasks')
        )
            return parent::getEloquentQuery();
        else
            return parent::getEloquentQuery()
                ->where('assigned_to', auth('web')->user()->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->label(__('message.project'))
                    ->relationship('project', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('title')
                    ->label(__('message.title'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('message.description'))
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Select::make('assigned_to')
                    ->label(__('message.assigned_to'))
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__('message.status'))
                    ->options([
                        TaskStatus::PENDING->value => __('message.pending'),
                        TaskStatus::INPROGRESS->value => __('message.in_progress'),
                        TaskStatus::DONE->value => __('message.done'),
                        TaskStatus::DELAYED->value => __('message.delayed'),
                    ])
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('message.start_date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('message.end_date'))
                    ->required(),
                Forms\Components\Hidden::make('created_by')
                    ->default(auth('web')->user()->id),
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
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('message.project'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('message.title'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label(__('message.assigned_to'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('message.start_date'))
                    ->date('d/m/y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('message.end_date'))
                    ->date('d/m/y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('message.status'))
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            TaskStatus::PENDING->value => 'warning',
                            TaskStatus::INPROGRESS->value => 'info',
                            TaskStatus::DONE->value => 'success',
                            TaskStatus::DELAYED->value => 'danger',
                            default => 'secondary',
                        };
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        TaskStatus::PENDING->value => __('message.pending'),
                        TaskStatus::INPROGRESS->value => __('message.in_progress'),
                        TaskStatus::DONE->value => __('message.done'),
                        TaskStatus::DELAYED->value => __('message.delayed'),
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('message.created_by'))
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
                Tables\Actions\Action::make('show_details')
                    ->label(__('message.show_details'))
                    ->icon('heroicon-o-information-circle')
                    ->color('gray')
                    ->modalHeading(__('message.task_details'))
                    ->modalCancelActionLabel(__('message.close'))
                    ->visible(fn($record) => true)
                    ->modalContent(function ($record) {
                        $html = '<div class="space-y-6 text-sm text-gray-800">';

                        $html .= '
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-2">' . __('message.task_info') . '</h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">';

                        $info = [
                            __('message.project') => optional($record->project)->name,
                            __('message.title') => $record->title,
                            __('message.description') => nl2br(e($record->description)),
                            __('message.assigned_to') => optional($record->assignedTo)->name,
                            __('message.status') => '<span ' . match ($record->status) {
                                TaskStatus::PENDING->value => 'style="color: #92400e; background-color: #fef3c7; border: 1px solid #facc15; padding: 0.125rem 0.5rem; border-radius: 9px;"',
                                TaskStatus::INPROGRESS->value => 'style="color: #1d4ed8; background-color: #dbeafe; border: 1px solid #3b82f6; padding: 0.125rem 0.5rem; border-radius: 9px;"',
                                TaskStatus::DELAYED->value => 'style="color: #b91c1c; background-color: #fee2e2; border: 1px solid #ef4444; padding: 0.125rem 0.5rem; border-radius: 9px;"',
                                TaskStatus::DONE->value => 'style="color: #15803d; background-color: #dcfce7; border: 1px solid #22c55e; padding: 0.125rem 0.5rem; border-radius: 9px;"',
                                default => 'style="color: #4b5563; background-color: #f3f4f6; border: 1px solid #9ca3af; padding: 0.125rem 0.5rem; border-radius: 9px;"',
                            } . '">' . __(match ($record->status) {
                                TaskStatus::PENDING->value => 'message.pending',
                                TaskStatus::INPROGRESS->value => 'message.in_progress',
                                TaskStatus::DELAYED->value => 'message.delayed',
                                TaskStatus::DONE->value => 'message.done',
                                default => $record->status,
                            }) . '</span>',
                            __('message.start_date') => $record->start_date,
                            __('message.end_date') => $record->end_date,
                        ];

                        foreach ($info as $label => $value) {
                            $isFullWidth = $label === __('message.description');
                            $colClass = $isFullWidth ? 'col-span-full' : '';
                            $html .= "
                        <div class='{$colClass}'>
                            <dt class='font-medium text-gray-600'>$label</dt>
                            <dd class='mt-1 text-gray-900'>" . $value . "</dd>
                        </div>";
                        }

                        $html .= '</dl></div>';

                        // ⬅️ الملفات
                        if ($record->files && $record->files->count() > 0) {
                            $html .= '
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 border-b pb-2">' . __('message.attached_files') . '</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">';

                            foreach ($record->files as $file) {
                                $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                                $url = asset('storage/' . $file->file_path);
                                $name = basename($file->file_path);
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico', 'bmp', 'tiff']);

                                $icon = match (true) {
                                    $isImage => '🖼️',
                                    in_array($ext, ['mp4', 'webm']) => '🎥',
                                    in_array($ext, ['mp3', 'wav', 'ogg']) => '🎵',
                                    $ext === 'pdf' => '📄',
                                    default => '📁',
                                };

                                $html .= "
                        <div class='bg-gray-50 border rounded-lg shadow-sm p-4'>
                            <div class='flex items-center justify-between mb-2'>
                                <span class='text-gray-700 text-sm font-medium'>$icon $name</span>
                                <a href='$url' target='_blank' class='text-blue-600 text-xs hover:underline'>" . __('message.view') . "</a>
                            </div>";

                                if ($isImage) {
                                    $html .= "<img src='$url' class='rounded-lg border h-32 bg-gray-50 p-1 mx-auto'>";
                                }

                                $html .= '</div>';
                            }

                            $html .= '</div></div>';
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('change_status')
                    ->label(__('message.change status'))
                    ->visible(function ($record) {
                        $user = auth('web')->user();

                        $hasNoPermission = !$user->can('edit tasks')
                            && !$user->can('create tasks')
                            && !$user->can('delete tasks');

                        return $hasNoPermission && $record->assigned_to === $user->id;
                    })
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->label(__('message.status'))
                            ->options([
                                TaskStatus::PENDING->value => __('message.pending'),
                                TaskStatus::INPROGRESS->value => __('message.in_progress'),
                                TaskStatus::DELAYED->value => __('message.delayed'),
                                TaskStatus::DONE->value => __('message.done'),
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, Model $record) {
                        $oldStatus = $record->status;
                        $newStatus = $data['status'];

                        $statusOrder = [
                            TaskStatus::PENDING->value => 1,
                            TaskStatus::INPROGRESS->value => 2,
                            TaskStatus::DELAYED->value => 3,
                            TaskStatus::DONE->value => 4,
                        ];

                        if ($statusOrder[$newStatus] < $statusOrder[$oldStatus]) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('message.status_change_denied'))
                                ->body(__('message.cannot_revert_status'))
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->status = $newStatus;
                        $record->save();

                        \Filament\Notifications\Notification::make()
                            ->title(__('message.status_changed_successfully'))
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary'),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return auth('web')->user()?->can('create tasks');
    }

    public static function canEdit(Model $record): bool
    {
        return auth('web')->user()?->can('edit tasks');
    }
    public static function canDelete(Model $record): bool
    {
        return auth('web')->user()?->can('delete tasks');
    }
}
