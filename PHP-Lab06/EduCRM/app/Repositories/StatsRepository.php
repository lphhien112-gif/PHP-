<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * EduCRM - StatsRepository (F5 - Dashboard Analytics)
 *
 * Chua cac truy van AGGREGATE phuc vu bieu do dashboard. KHONG doc $_POST/$_GET.
 * Tat ca query loc deleted_at IS NULL (ton trong soft delete F2).
 * Index ho tro: idx_leads_created_at / idx_orders_created_at / idx_*_status.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class StatsRepository
{
    private ?PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db;
    }

    private function db(): PDO
    {
        return $this->db ??= Database::connection();
    }

    /**
     * Lead theo ngay trong N ngay gan day. Tra mang [date => count] day du
     * (dien 0 cho ngay khong co lead) de view ve thanh lien tuc.
     *
     * @return array<string,int>  key 'YYYY-MM-DD'
     */
    public function leadsPerDay(int $days = 14): array
    {
        $days = max(1, min(60, $days));
        $sql = "SELECT DATE(created_at) AS d, COUNT(*) AS c
                FROM leads
                WHERE deleted_at IS NULL
                  AND created_at >= (CURRENT_DATE - INTERVAL :days DAY)
                GROUP BY DATE(created_at)";
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();

        $found = [];
        foreach ($stmt->fetchAll() as $r) {
            $found[(string) $r['d']] = (int) $r['c'];
        }

        // Dien day du tu (today - days + 1) -> today, gia tri 0 neu thieu.
        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-{$i} day"));
            $out[$day] = $found[$day] ?? 0;
        }
        return $out;
    }

    /**
     * Phieu chuyen doi (funnel): so lead theo tung trang thai trong chuoi
     * new -> contacted -> qualified -> converted.
     *
     * @return array<string,int>
     */
    public function conversionFunnel(): array
    {
        $stages = ['new', 'contacted', 'qualified', 'converted'];
        $rows = $this->db()->query(
            "SELECT status, COUNT(*) AS c FROM leads WHERE deleted_at IS NULL GROUP BY status"
        )->fetchAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['status']] = (int) $r['c'];
        }
        $out = [];
        foreach ($stages as $st) {
            $out[$st] = $map[$st] ?? 0;
        }
        return $out;
    }

    /**
     * Doanh thu (status=paid) 6 thang gan nhat, theo thang.
     *
     * @return array<string,float>  key 'YYYY-MM'
     */
    public function revenueLastMonths(int $months = 6): array
    {
        $months = max(1, min(24, $months));
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COALESCE(SUM(amount),0) AS revenue
                FROM orders
                WHERE deleted_at IS NULL AND status = 'paid'
                  AND created_at >= (DATE_FORMAT(CURRENT_DATE, '%Y-%m-01') - INTERVAL :m MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')";
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':m', $months - 1, PDO::PARAM_INT);
        $stmt->execute();

        $found = [];
        foreach ($stmt->fetchAll() as $r) {
            $found[(string) $r['ym']] = (float) $r['revenue'];
        }

        $out = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime("first day of -{$i} month"));
            $out[$ym] = $found[$ym] ?? 0.0;
        }
        return $out;
    }

    /**
     * Top N khoa hoc theo so phieu (kem tong doanh thu).
     *
     * @return array<int,array{course:string,orders:int,revenue:float}>
     */
    public function topCourses(int $limit = 5): array
    {
        $limit = max(1, min(20, $limit));
        $sql = "SELECT course,
                       COUNT(*) AS orders,
                       COALESCE(SUM(amount),0) AS revenue
                FROM orders
                WHERE deleted_at IS NULL
                GROUP BY course
                ORDER BY orders DESC, revenue DESC
                LIMIT :lim";
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $out = [];
        foreach ($stmt->fetchAll() as $r) {
            $out[] = [
                'course'  => (string) $r['course'],
                'orders'  => (int) $r['orders'],
                'revenue' => (float) $r['revenue'],
            ];
        }
        return $out;
    }
}
