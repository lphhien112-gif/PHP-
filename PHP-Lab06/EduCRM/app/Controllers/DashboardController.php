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
    public function index(): void
    {
        require_login();

        $leadStats   = (new LeadService())->stats();
        $orderStats  = (new OrderService())->stats();
        $courseStats = (new CourseService())->stats(); // Module C
        $analytics   = (new DashboardService())->analytics(); // F5

        echo render('dashboard/index', [
            'title'       => 'Tong quan',
            'leadStats'   => $leadStats,
            'orderStats'  => $orderStats,
            'courseStats' => $courseStats,
            'analytics'   => $analytics,
        ]);
    }
}
