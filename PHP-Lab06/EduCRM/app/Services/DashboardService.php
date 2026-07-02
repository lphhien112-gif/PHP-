<?php

namespace App\Services;

use App\Repositories\StatsRepository;
use App\Repositories\LeadRepository;

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
    private LeadRepository $leads;

    public function __construct(?StatsRepository $stats = null, ?LeadRepository $leads = null)
    {
        $this->stats = $stats ?? new StatsRepository();
        $this->leads = $leads ?? new LeadRepository();
    }

    public function analytics(): array
    {
        return [
            'leadsPerDay' => $this->stats->leadsPerDay(14),
            'funnel'      => $this->stats->conversionFunnel(),
            'revenue'     => $this->stats->revenueLastMonths(6),
            'topCourses'  => $this->stats->topCourses(5),
            'bySource'    => $this->leads->countBySource(),
        ];
    }

    /**
     * KPI co xu huong cho trang Tong quan (khong chi so tinh):
     *   - Ty le chuyen doi = converted / tong lead
     *   - Ty le thanh toan = paid / tong phieu
     *   - Doanh thu thang nay vs thang truoc (delta %)
     * Nhan san leadStats/orderStats (Controller da lay) de khong query lai.
     */
    public function summary(array $leadStats, array $orderStats): array
    {
        $leadTotal  = (int) ($leadStats['total'] ?? 0);
        $converted  = (int) ($leadStats['byStatus']['converted'] ?? 0);
        $orderTotal = (int) ($orderStats['total'] ?? 0);
        $paid       = (int) ($orderStats['byStatus']['paid'] ?? 0);

        // revenueLastMonths(2) tra [thang_truoc => x, thang_nay => y] (thu tu tang dan).
        $rev  = array_values($this->stats->revenueLastMonths(2));
        $prev = (float) ($rev[0] ?? 0);
        $cur  = (float) ($rev[1] ?? 0);
        // Delta %: chi so sanh duoc khi thang truoc > 0; nguoc lai tra null.
        $deltaPct = $prev > 0 ? (($cur - $prev) / $prev * 100) : null;

        return [
            'conversionRate'   => $leadTotal  > 0 ? $converted / $leadTotal * 100 : 0.0,
            'convertedCount'   => $converted,
            'leadTotal'        => $leadTotal,
            'paymentRate'      => $orderTotal > 0 ? $paid / $orderTotal * 100 : 0.0,
            'paidCount'        => $paid,
            'orderTotal'       => $orderTotal,
            'revenueThisMonth' => $cur,
            'revenueLastMonth' => $prev,
            'revenueDeltaPct'  => $deltaPct,
        ];
    }
}
