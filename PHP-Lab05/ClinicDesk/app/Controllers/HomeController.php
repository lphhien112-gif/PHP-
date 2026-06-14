<?php

namespace ClinicDesk\Controllers;

use ClinicDesk\Core\Database;
use ClinicDesk\Core\Response;
use ClinicDesk\Core\View;
use ClinicDesk\Repositories\PatientRepository;
use ClinicDesk\Repositories\AppointmentRepository;

/**
 * HomeController - GET / : dashboard gioi thieu 2 module + thong ke nhanh.
 */
class HomeController
{
    public function index(): void
    {
        $stats = ['patients' => null, 'appointments' => null, 'connected' => false];

        // Lay so lieu thong ke neu DB song; neu loi van hien dashboard (so = ?).
        try {
            $stats['connected']    = Database::isConnected();
            if ($stats['connected']) {
                $stats['patients']     = (new PatientRepository())->countAll();
                $stats['appointments'] = (new AppointmentRepository())->countAll();
            }
        } catch (\Throwable $e) {
            $stats['connected'] = false;
        }

        $html = View::render('home', [
            'title' => 'ClinicDesk - Trang chu',
            'stats' => $stats,
        ]);
        Response::html($html);
    }
}
