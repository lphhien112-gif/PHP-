<?php

namespace EventHub\Helpers;

/**
 * Validator - Kiểm tra dữ liệu đầu vào
 * EventHub - Mini Event Booking App
 */
class Validator
{
    private array $errors = [];

    public function required(string $field, mixed $value): self
    {
        if (empty($value) && $value !== '0' && $value !== 0) {
            $this->errors[$field][] = "Trường '{$field}' là bắt buộc.";
        }
        return $this;
    }

    public function minLength(string $field, string $value, int $min): self
    {
        if (strlen(trim($value)) < $min) {
            $this->errors[$field][] = "Trường '{$field}' phải có ít nhất {$min} ký tự.";
        }
        return $this;
    }

    public function email(string $field, string $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "Trường '{$field}' phải là địa chỉ email hợp lệ.";
        }
        return $this;
    }

    public function isInteger(string $field, mixed $value): self
    {
        if (!is_numeric($value) || (int)$value != $value) {
            $this->errors[$field][] = "Trường '{$field}' phải là số nguyên.";
        }
        return $this;
    }

    public function isPositive(string $field, mixed $value): self
    {
        if ((int)$value <= 0) {
            $this->errors[$field][] = "Trường '{$field}' phải là số dương.";
        }
        return $this;
    }

    public function phone(string $field, string $value): self
    {
        if (!preg_match('/^[0-9+\-\s()]{7,15}$/', trim($value))) {
            $this->errors[$field][] = "Trường '{$field}' phải là số điện thoại hợp lệ.";
        }
        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
