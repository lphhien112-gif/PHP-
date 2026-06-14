<?php

namespace App\Controllers;

use App\Services\AuditService;

/**
 * EduCRM - AuditController (F3 - Nhat ky hoat dong)
 *
 * THIN controller: chi ADMIN moi xem duoc. Doc request -> goi AuditService
 * -> render. KHONG co SQL.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class AuditController
{
    private AuditService $audit;

    public function __construct()
    {
        $this->audit = new AuditService();
    }

    /** GET /audit - phan trang + filter user/action/entity (admin-only). */
    public function index(): void
    {
        require_login();
        // F11: Enforce SERVER-SIDE - chi admin (khong chi an nut o UI).
        require_can('audit', '/dashboard');

        $data = $this->audit->list($_GET);
        $data['title'] = 'Nhat ky hoat dong';
        echo render('audit/index', $data);
        clear_old();
    }
}
