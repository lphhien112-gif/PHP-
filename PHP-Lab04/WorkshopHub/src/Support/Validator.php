<?php

namespace WorkshopHub\Support;

/**
 * WorkshopHub — Server-side Validator
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien  —  MSSV: 22110059
 * Mon hoc   : Lap trinh Web      —  GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 * Validate server-side: required, email format, phone pattern,
 * length, in-list. Tra ve mang loi theo tung field.
 */
class Validator
{
    /** @var array<string,string> field => thong bao loi (mot loi/field) */
    private array $errors = [];

    /** @var array<string,string> du lieu da trim */
    private array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    private function value(string $field): string
    {
        return trim((string) ($this->data[$field] ?? ''));
    }

    /** Chi ghi loi dau tien cho moi field (tranh chong loi). */
    private function add(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function required(string $field, string $label): self
    {
        if ($this->value($field) === '') {
            $this->add($field, "$label la bat buoc.");
        }
        return $this;
    }

    public function email(string $field, string $label): self
    {
        $val = $this->value($field);
        if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->add($field, "$label khong dung dinh dang email.");
        }
        return $this;
    }

    /** Phone VN: bat dau bang 0, theo sau 9 chu so (10 so tong). */
    public function phone(string $field, string $label): self
    {
        $val = $this->value($field);
        if ($val !== '' && !preg_match('/^0\d{9}$/', $val)) {
            $this->add($field, "$label phai gom 10 chu so va bat dau bang 0.");
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label): self
    {
        $val = $this->value($field);
        if ($val !== '' && mb_strlen($val) < $min) {
            $this->add($field, "$label phai co it nhat $min ky tu.");
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label): self
    {
        $val = $this->value($field);
        if ($val !== '' && mb_strlen($val) > $max) {
            $this->add($field, "$label khong vuot qua $max ky tu.");
        }
        return $this;
    }

    /** Gia tri phai nam trong danh sach cho phep (in-list). */
    public function inList(string $field, array $allowed, string $label): self
    {
        $val = $this->value($field);
        if ($val !== '' && !in_array($val, $allowed, true)) {
            $this->add($field, "$label khong hop le.");
        }
        return $this;
    }
}
