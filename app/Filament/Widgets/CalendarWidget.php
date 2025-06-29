<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Enum\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 2;

    public Model|string|int|null $record = null;
    public function mount(): void
    {
        $this->record = new Task();
    }
    public function fetchEvents(array $fetchInfo): array
    {
        $user = auth('web')->user();
        $query = Task::query()->with('assignedTo');

        if (!$user->hasRole('admin'))
            $query->where('assigned_to', $user->id);


        return $query->get()->map(function ($task) {
            $color = match ($task->status) {
                TaskStatus::PENDING->value => '#f59e0b',
                TaskStatus::INPROGRESS->value => '#3b82f6',
                TaskStatus::DONE->value => '#10b981',
                TaskStatus::DELAYED->value => '#ef4444',
                default => '#6b7280',
            };

            return [
                'id'    => $task->id,
                'title' => $task->title . ($task->assignedTo ? ' - ' . $task->assignedTo->name : ''),
                'start' => $task->start_date,
                'end'   => $task->end_date,
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
            ];
        })->toArray();
    }
    public function onEventClick(array $event): void
    {
        $this->record = Task::find($event['id']);

        $this->mountAction('view');
    }

    public function viewAction(): Action
    {
        return Action::make('view')
            ->label(__('message.view_details'))
            ->modalHeading(fn() => __('message.task_details'))
            ->modalSubmitAction(false)
            ->modalContent(function () {
                if (!$this->record) {
                    return new HtmlString('<div class="p-6 text-center text-gray-500">' . __('message.no_task_to_show') . '</div>');
                }

                $status = $this->record->status;
                $statusLabel = __('message.' . $status);
                $badgeClass = match ($status) {
                    TaskStatus::PENDING->value => "fi-badge fi-badge-warning",
                    TaskStatus::INPROGRESS->value => "fi-badge fi-badge-info",
                    TaskStatus::DONE->value => "fi-badge fi-badge-success",
                    TaskStatus::DELAYED->value => "fi-badge fi-badge-danger",
                    default => "fi-badge fi-badge-secondary",
                };

                $statusColor = match ($status) {
                    TaskStatus::PENDING->value => "#f59e0b",
                    TaskStatus::INPROGRESS->value => "#3b82f6",
                    TaskStatus::DONE->value => "#10b981",
                    TaskStatus::DELAYED->value => "#ef4444",
                    default => "#6b7280",
                };

                $description = $this->record->description
                    ? e($this->record->description)
                    : '<span class="text-gray-400">' . __('message.no_description') . '</span>';

                $assignedTo = $this->record->assignedTo?->name
                    ? e($this->record->assignedTo->name)
                    : '<span class="text-gray-400">' . __('message.not_assigned') . '</span>';

                return new HtmlString('
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="bg-gray-900 p-6 -m-6 mb-6 rounded-t-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded-full border-2 border-white" style="background-color:' . $statusColor . ';"></div>
                                <h2 class="text-xl font-bold">' . e($this->record->title) . '</h2>
                            </div>
                            
                        </div>
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-700 dark:border-gray-700 text-white">
                            <span class="w-2 h-2 rounded-full mr-2" style="background-color:' . $statusColor . ';"></span>
                            <span style="color:' . $statusColor . ';">' . $statusLabel . '</span>
                        </div>
                    </div>
                    
                    <!-- Description Card -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border-l-4" style="border-left-color: ' . $statusColor . ';">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">' . __('message.description') . '</h4>
                                <div class="text-gray-700 dark:text-white">' . $description . '</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date Range Card -->
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4 border border-emerald-200 dark:border-emerald-800">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">المدة الزمنية</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">' . __('message.from') . ':</span>
                                <span class="bg-white dark:bg-gray-800 px-3 py-1 rounded-lg text-sm font-medium text-gray-900 dark:text-gray-100 shadow-sm border dark:border-gray-600">' . e(optional($this->record->start_date)->format('d/m/Y')) . '</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">' . __('message.to') . ':</span>
                                <span class="bg-white dark:bg-gray-800 px-3 py-1 rounded-lg text-sm font-medium text-gray-900 dark:text-gray-100 shadow-sm border dark:border-gray-600">' . e(optional($this->record->end_date)->format('d/m/Y')) . '</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Assigned To Card -->
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">' . __('message.assigned_to') . '</h4>
                                <div class="text-purple-700 dark:text-purple-300 font-medium">' . $assignedTo . '</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <span>' . __('message.last_update') . ' : ' . e(optional($this->record->updated_at)->format('d/m/Y H:i')) . '</span>
                    </div>
                </div>
            ');
            });
    }
}
