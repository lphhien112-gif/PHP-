<?php

namespace App\Controllers;

use App\Services\LeadService;
use App\Services\OrderService;
use App\Services\CourseService;
use App\Services\DashboardService;

/**
 * EduCRM - DashboardController
 *
 * Trang tong quan yeu cau dang nhap. Hien thong ke nhanh (counts) bang cach
 * goi Service - khong truy van DB truc tiep.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class DashboardController
{
    /** GET /dashboard - Tong quan: hero + cac o KPI nhanh. */
    public function index(): void
    {
        require_login();

        echo render('dashboard/index', [
            'title'       => 'Tong quan',
            'leadStats'   => (new LeadService())->stats(),
            'orderStats'  => (new OrderService())->stats(),
            'courseStats' => (new CourseService())->stats(),
        ]);
    }

    /** GET /analytics - Thong ke: bieu do + bang phan tich (F5). */
    public function analytics(): void
    {
        require_login();

        echo render('analytics/index', [
            'title'      => 'Thong ke',
            'leadStats'  => (new LeadService())->stats(),
            'orderStats' => (new OrderService())->stats(),
            'analytics'  => (new DashboardService())->analytics(),
        ]);
    }
}
