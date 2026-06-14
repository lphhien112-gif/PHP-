<?php

namespace ClinicDesk\Controllers;

use ClinicDesk\Core\Flash;
use ClinicDesk\Core\Paginator;
use ClinicDesk\Core\Response;
use ClinicDesk\Core\View;
use ClinicDesk\Repositories\PatientRepository;
use PDOException;

/**
 * PatientController - CRUD module A (benh nhan).
 *
 * Luong Create/Update: request -> validate -> repository -> redirect (PRG).
 * Duplicate email: bat PDOException SQLSTATE 23000 -> loi field-level, giu input cu.
 */
class PatientController
{
    private PatientRepository $repo;
    private int $perPage;

    public function __construct()
    {
        $this->repo = new PatientRepository();
        $cfg = require __DIR__ . '/../../config/app.php';
        $this->perPage = (int)$cfg['per_page'];
    }

    /** GET /patients - List + search + sort + pagination. */
    public function index(): void
    {
        $q         = trim((string)($_GET['q'] ?? ''));
        $sortKey   = $this->repo->sortKey($_GET['sort'] ?? null);       // qua whitelist
        $direction = $this->repo->resolveDirection($_GET['direction'] ?? null);
        $rawPage   = (int)($_GET['page'] ?? 1);

        $total = $this->repo->countAll($q);
        $pager = new Paginator($rawPage, $this->perPage, $total);       // clamp page

        $patients = $this->repo->paginate(
            $q,
            $sortKey,
            $direction,
            $pager->perPage,
            $pager->offset
        );

        $html = View::render('patients/index', [
            'title'     => 'Benh nhan - ClinicDesk',
            'patients'  => $patients,
            'q'         => $q,
            'sort'      => $sortKey,
            'direction' => $direction,
            'pager'     => $pager,
            'flash_success' => Flash::pull('success'),
            'flash_error'   => Flash::pull('error'),
        ]);
        Response::html($html);
    }

    /** GET /patients/create - Form tao moi. */
    public function create(): void
    {
        $html = View::render('patients/create', [
            'title'  => 'Them benh nhan - ClinicDesk',
            'old'    => $this->emptyForm(),
            'errors' => [],
            'action' => '/patients/store',
            'mode'   => 'create',
        ]);
        Response::html($html);
    }

    /** POST /patients/store - Validate + INSERT + redirect/form loi. */
    public function store(): void
    {
        $data   = $this->collect();
        $errors = $this->validate($data);

        if ($errors) {
            $this->renderForm('/patients/store', 'create', $data, $errors, 422);
            return;
        }

        try {
            $this->repo->create($data);
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                $errors['email'] = 'Email nay da ton tai. Vui long dung email khac.';
                $this->renderForm('/patients/store', 'create', $data, $errors, 409);
                return;
            }
            throw $e; // loi khac -> Router bat -> 500 safe message + log.
        }

        Flash::success('Da them benh nhan "' . $data['name'] . '" thanh cong.');
        Response::redirect('/patients'); // PRG
    }

    /** GET /patients/edit?id= - Form sua co du lieu cu. */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $patient = $id > 0 ? $this->repo->find($id) : null;

        if (!$patient) {
            Response::notFound('/patients/edit?id=' . $id);
            return;
        }

        $html = View::render('patients/edit', [
            'title'  => 'Sua benh nhan - ClinicDesk',
            'old'    => $patient,
            'errors' => [],
            'action' => '/patients/update',
            'mode'   => 'edit',
        ]);
        Response::html($html);
    }

    /** POST /patients/update - Validate + UPDATE + redirect/form loi. */
    public function update(): void
    {
        $id   = (int)($_POST['id'] ?? 0);
        $data = $this->collect();
        $data['id'] = $id;

        $existing = $id > 0 ? $this->repo->find($id) : null;
        if (!$existing) {
            Response::notFound('/patients/update');
            return;
        }

        $errors = $this->validate($data);
        if ($errors) {
            $this->renderForm('/patients/update', 'edit', $data, $errors, 422);
            return;
        }

        try {
            $this->repo->update($id, $data);
        } catch (PDOException $e) {
            if ($this->isDuplicate($e)) {
                $errors['email'] = 'Email nay da thuoc ve benh nhan khac.';
                $this->renderForm('/patients/update', 'edit', $data, $errors, 409);
                return;
            }
            throw $e;
        }

        Flash::success('Da cap nhat benh nhan "' . $data['name'] . '".');
        Response::redirect('/patients'); // PRG
    }

    /** POST /patients/delete - Xoa bang POST + redirect. */
    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $affected = $this->repo->delete($id);
            if ($affected > 0) {
                Flash::success('Da xoa benh nhan #' . $id . '.');
            } else {
                Flash::error('Khong tim thay benh nhan #' . $id . ' de xoa.');
            }
        } else {
            Flash::error('ID benh nhan khong hop le.');
        }
        Response::redirect('/patients'); // PRG
    }

    // ---------------- helpers ----------------

    private function emptyForm(): array
    {
        return ['name' => '', 'email' => '', 'phone' => '', 'gender' => 'other', 'address' => ''];
    }

    /** Gom du lieu tu $_POST (chi cot can thiet). */
    private function collect(): array
    {
        return [
            'name'    => trim((string)($_POST['name'] ?? '')),
            'email'   => trim((string)($_POST['email'] ?? '')),
            'phone'   => trim((string)($_POST['phone'] ?? '')),
            'gender'  => (string)($_POST['gender'] ?? 'other'),
            'address' => trim((string)($_POST['address'] ?? '')),
        ];
    }

    /** Validate server-side. Tra mang loi theo field. */
    private function validate(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Vui long nhap ho ten.';
        }
        if ($data['email'] === '') {
            $errors['email'] = 'Vui long nhap email.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email khong dung dinh dang.';
        }
        if ($data['phone'] === '') {
            $errors['phone'] = 'Vui long nhap so dien thoai.';
        } elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $data['phone'])) {
            $errors['phone'] = 'So dien thoai khong hop le.';
        }
        if (!in_array($data['gender'], ['male', 'female', 'other'], true)) {
            $errors['gender'] = 'Gioi tinh khong hop le.';
        }
        return $errors;
    }

    private function isDuplicate(PDOException $e): bool
    {
        // SQLSTATE 23000 = integrity constraint violation (vi pham UNIQUE).
        return $e->getCode() === '23000';
    }

    private function renderForm(string $action, string $mode, array $old, array $errors, int $status): void
    {
        $view = $mode === 'edit' ? 'patients/edit' : 'patients/create';
        $html = View::render($view, [
            'title'  => ($mode === 'edit' ? 'Sua' : 'Them') . ' benh nhan - ClinicDesk',
            'old'    => $old,
            'errors' => $errors,
            'action' => $action,
            'mode'   => $mode,
        ]);
        Response::html($html, $status);
    }
}
