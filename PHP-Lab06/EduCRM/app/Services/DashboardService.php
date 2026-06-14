<?php

namespace App\Services;

use App\Repositories\StatsRepository;

/**
 * EduCRM - DashboardService (F5 - Dashboard Analytics)
 *
 * Gom toan bo so lieu cho dashboard tu StatsRepository thanh 1 mang san
 * de view chi viec ve. KHONG SQL o day (Repository lo SQL).
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class DashboardService
{
    private StatsRepository $stats;

    public function __construct(?StatsRepository $stats = null)
    {
        $this->stats = $stats ?? new StatsRepository();
    }

    public function analytics(): array
    {
        return [
            'leadsPerDay' => $this->stats->leadsPerDay(14),
            'funnel'      => $this->stats->conversionFunnel(),
            'revenue'     => $this->stats->revenueLastMonths(6),
            'topCourses'  => $this->stats->topCourses(5),
        ];
    }
}
