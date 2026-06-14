<?php

namespace ClinicDesk\Controllers;

use ClinicDesk\Core\Flash;
use ClinicDesk\Core\Paginator;
use ClinicDesk\Core\Response;
use ClinicDesk\Core\View;
use ClinicDesk\Repositories\AppointmentRepository;
use PDOException;

/**
 * AppointmentController - CRUD module B (lich hen).
 *
 * Co them filter theo status. appointment_code UNIQUE -> chan trung ma.
 */
class AppointmentController
{
    private AppointmentRepository $repo;
    private int $perPage;

    public function __construct()
    {
        $this->repo = new AppointmentRepository();
        $cfg = require __DIR__ . '/../../config/app.php';
        $this->perPage = (int)$cfg['per_page'];
    }

    /** GET /appointments - List + search + status filter + sort + pagination. */
    public function index(): void
    {
        $q         = trim((string)($_GET['q'] ?? ''));
        $status    = (string)($_GET['status'] ?? '');
        if (!in_array($status, AppointmentRepository::STATUSES, true)) {
            $status = ''; // status khong hop le -> bo qua filter.
        }
        $sortKey   = $this->repo->sortKey($_GET['sort'] ?? null);
        $direction = $this->repo->resolveDirection($_GET['direction'] ?? null);
        $rawPage   = (int)($_GET['page'] ?? 1);

        $total = $this->repo->countAll($q, $status);
        $pager = new Paginator($rawPage, $this->perPage, $total);

        $appointments = $this->repo->paginate(
            $q, $status, $sortKey, $direction, $pager->perPage, $pager->offset
        );

        $html = View::render('appointments/index', [
            'title'        => 'Lich hen - ClinicDesk',
            'appointments' => $appointments,
            'q'            => $q,
            'status'       => $status,
            'statuses'     => AppointmentRepository::STATUSES,
            'sort'         => $sortKey,
            'direction'    => $direction,
            'pager'        => $pager,
            'flash_success'=> Flash::pull('success'),
            'flash_error'  => Flash::pull('error'),
        ]);
        Response::html($html);
    }

    /** GET /appointments/create. */
    public function create(): void
    {
        $html = View::render('appointments/create', [
            'title'       => 'Tao lich hen - ClinicDesk',
            'old'         => $this->emptyForm(),
            'errors'      => [],
            'action'      => '/appointments/store',
            'mode'        => 'create',
            'statuses'    => AppointmentRepository::STATUSES,
            'departments' => AppointmentRepository::DEPARTMENTS,
        ]);
        Response::html($html);
    }

    /** POST /appointments/store. */
    public function store(): void
    {
        $data   = $this->collect();
        $errors = $this->validate($data);

        if ($errors) {
            $this->renderForm('/appointments/store', 'create', $data, $errors, 422);
            return;
        }

        try {
            $this->repo->create($data);
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                $errors['appointment_code'] = 'Ma lich hen nay da ton tai. Vui long dung ma khac.';
                $this->renderForm('/appointments/store', 'create', $data, $errors, 409);
                return;
            }
            throw $e;
        }

        Flash::success('Da tao lich hen ' . $data['appointment_code'] . ' thanh cong.');
        Response::redirect('/appointments'); // PRG
    }

    /** GET /appointments/edit?id=. */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $appt = $id > 0 ? $this->repo->find($id) : null;
        if (!$appt) {
            Response::notFound('/appointments/edit?id=' . $id);
            return;
        }
        // Chuyen datetime DB (Y-m-d H:i:s) sang dinh dang input datetime-local.
        $appt['appointment_date_input'] = date('Y-m-d\TH:i', strtotime($appt['appointment_date']));

        $html = View::render('appointments/edit', [
            'title'       => 'Sua lich hen - ClinicDesk',
            'old'         => $appt,
            'errors'      => [],
            'action'      => '/appointments/update',
            'mode'        => 'edit',
            'statuses'    => AppointmentRepository::STATUSES,
            'departments' => AppointmentRepository::DEPARTMENTS,
        ]);
        Response::html($html);
    }

    /** POST /appointments/update. */
    public function update(): void
    {
        $id   = (int)($_POST['id'] ?? 0);
        $data = $this->collect();
        $data['id'] = $id;

        $existing = $id > 0 ? $this->repo->find($id) : null;
        if (!$existing) {
            Response::notFound('/appointments/update');
            return;
        }

        $errors = $this->validate($data);
        if ($errors) {
            $data['appointment_date_input'] = $_POST['appointment_date'] ?? '';
            $this->renderForm('/appointments/update', 'edit', $data, $errors, 422);
            return;
        }

        try {
            $this->repo->update($id, $data);
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                $errors['appointment_code'] = 'Ma lich hen nay da thuoc ve lich khac.';
                $data['appointment_date_input'] = $_POST['appointment_date'] ?? '';
                $this->renderForm('/appointments/update', 'edit', $data, $errors, 409);
                return;
            }
            throw $e;
        }

        Flash::success('Da cap nhat lich hen ' . $data['appointment_code'] . '.');
        Response::redirect('/appointments'); // PRG
    }

    /** POST /appointments/delete. */
    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $affected = $this->repo->delete($id);
            if ($affected > 0) {
                Flash::success('Da xoa lich hen #' . $id . '.');
            } else {
                Flash::error('Khong tim thay lich hen #' . $id . ' de xoa.');
            }
        } else {
            Flash::error('ID lich hen khong hop le.');
        }
        Response::redirect('/appointments'); // PRG
    }

    // ---------------- helpers ----------------

    private function emptyForm(): array
    {
        return [
            'appointment_code'       => '',
            'patient_name'           => '',
            'patient_email'          => '',
            'department'             => AppointmentRepository::DEPARTMENTS[0],
            'appointment_date'       => '',
            'appointment_date_input' => '',
            'status'                 => 'pending',
            'note'                   => '',
        ];
    }

    private function collect(): array
    {
        $rawDate = trim((string)($_POST['appointment_date'] ?? ''));
        // input datetime-local cho 'Y-m-d\TH:i' -> chuyen ve 'Y-m-d H:i:s' cho DB.
        $dbDate = '';
        if ($rawDate !== '') {
            $ts = strtotime($rawDate);
            $dbDate = $ts ? date('Y-m-d H:i:s', $ts) : '';
        }
        return [
            'appointment_code' => strtoupper(trim((string)($_POST['appointment_code'] ?? ''))),
            'patient_name'     => trim((string)($_POST['patient_name'] ?? '')),
            'patient_email'    => trim((string)($_POST['patient_email'] ?? '')),
            'department'       => trim((string)($_POST['department'] ?? '')),
            'appointment_date' => $dbDate,
            'status'           => (string)($_POST['status'] ?? 'pending'),
            'note'             => trim((string)($_POST['note'] ?? '')),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['appointment_code'] === '') {
            $errors['appointment_code'] = 'Vui long nhap ma lich hen.';
        } elseif (!preg_match('/^[A-Z0-9\-]{4,20}$/', $data['appointment_code'])) {
            $errors['appointment_code'] = 'Ma lich hen chi gom chu hoa, so va dau gach (4-20 ky tu).';
        }
        if ($data['patient_name'] === '') {
            $errors['patient_name'] = 'Vui long nhap ten benh nhan.';
        }
        if ($data['patient_email'] === '') {
            $errors['patient_email'] = 'Vui long nhap email benh nhan.';
        } elseif (!filter_var($data['patient_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['patient_email'] = 'Email khong dung dinh dang.';
        }
        if ($data['department'] === '') {
            $errors['department'] = 'Vui long chon chuyen khoa.';
        }
        if ($data['appointment_date'] === '') {
            $errors['appointment_date'] = 'Vui long chon ngay gio kham hop le.';
        }
        if (!in_array($data['status'], AppointmentRepository::STATUSES, true)) {
            $errors['status'] = 'Trang thai khong hop le.';
        }
        return $errors;
    }

    private function isDuplicate(PDOException $e): bool
    {
        return $e->getCode() === '23000';
    }

    private function renderForm(string $action, string $mode, array $old, array $errors, int $status): void
    {
        $view = $mode === 'edit' ? 'appointments/edit' : 'appointments/create';
        if (!isset($old['appointment_date_input'])) {
            $old['appointment_date_input'] = $_POST['appointment_date'] ?? '';
        }
        $html = View::render($view, [
            'title'       => ($mode === 'edit' ? 'Sua' : 'Tao') . ' lich hen - ClinicDesk',
            'old'         => $old,
            'errors'      => $errors,
            'action'      => $action,
            'mode'        => $mode,
            'statuses'    => AppointmentRepository::STATUSES,
            'departments' => AppointmentRepository::DEPARTMENTS,
        ]);
        Response::html($html, $status);
    }
}
