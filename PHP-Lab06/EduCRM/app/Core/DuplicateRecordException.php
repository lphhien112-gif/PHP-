<?php

namespace App\Core;

use RuntimeException;

/**
 * EduCRM - DuplicateRecordException
 *
 * Nem ra khi vi pham UNIQUE constraint (vi du trung email lead hoac
 * trung order_code). Service bat exception nay roi bien thanh thong bao
 * loi than thien gan voi field cu the - KHONG lo SQLSTATE / ten bang.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class DuplicateRecordException extends RuntimeException
{
    /** Ten field bi trung (vi du: 'email', 'order_code') */
    private string $field;

    public function __construct(string $field, string $message = '')
    {
        $this->field = $field;
        parent::__construct($message !== '' ? $message : "Gia tri truong $field da ton tai.");
    }

    public function field(): string
    {
        return $this->field;
    }
}
