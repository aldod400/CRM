<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Enum\ProjectStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth('web')->user()?->hasRole('admin') ?? false;
    }

    protected function getStats(): array
    {
        // عدد العملاء
        $totalClients = Client::count();

        // عدد المشاريع المكتملة
        $completedProjects = Project::where('status', ProjectStatus::COMPLETED)->count();

        // عدد المشاريع تحت التنفيذ
        $inProgressProjects = Project::where('status', ProjectStatus::INPROGRESS)->count();

        // إجمالي الفواتير
        $totalInvoiceAmount = Invoice::sum('amount');

        // Charts ديناميكية للـ 7 أيام الماضية
        $clientsChart = $this->getClientsChart();
        $completedProjectsChart = $this->getCompletedProjectsChart();
        $inProgressProjectsChart = $this->getInProgressProjectsChart();
        $invoicesChart = $this->getInvoicesChart();

        return [
            Stat::make(__('message.total_clients'), $totalClients)
                ->description(__('message.total_clients_desc'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart($clientsChart),

            Stat::make(__('message.completed_projects'), $completedProjects)
                ->description(__('message.completed_projects_desc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart($completedProjectsChart),

            Stat::make(__('message.inprogress_projects'), $inProgressProjects)
                ->description(__('message.inprogress_projects_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart($inProgressProjectsChart),

            Stat::make(__('message.total_invoices'), number_format($totalInvoiceAmount, 2))
                ->description(__('message.total_invoices_desc'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary')
                ->chart($invoicesChart),
        ];
    }

    private function getClientsChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Client::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getCompletedProjectsChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Project::where('status', ProjectStatus::COMPLETED)
                ->whereDate('updated_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getInProgressProjectsChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Project::where('status', ProjectStatus::INPROGRESS)
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getInvoicesChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $amount = Invoice::whereDate('created_at', $date)->sum('amount');
            $data[] = intval($amount / 1000); // قسمة على 1000 لجعل الأرقام أصغر في الـ chart
        }
        return $data;
    }
}
