<?php

namespace App\Core;

use RuntimeException;

/**
 * EduCRM - ConvertConflictException
 *
 * Nem ra khi viec chuyen lead -> phieu (F1) gap tinh trang lead da bi
 * chuyen doi/mat boi mot thao tac khac (double-submit / race). Service bat
 * exception nay de rollback transaction va tra thong bao than thien, tranh
 * tao 2 phieu cho cung mot lead. KHONG lo SQLSTATE / ten bang.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class ConvertConflictException extends RuntimeException
{
}
