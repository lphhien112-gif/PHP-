<?php

namespace ClinicDesk\Core;

/**
 * Paginator - Chuan hoa tham so phan trang an toan.
 *
 * - Clamp page <= 0 ve 1.
 * - Clamp page > totalPages ve totalPages.
 * - Tinh offset = (page - 1) * perPage.
 *
 * Day la cho duy nhat xu ly "page am" hoac "page qua lon".
 */
class Paginator
{
    public int $page;
    public int $perPage;
    public int $total;
    public int $totalPages;
    public int $offset;

    public function __construct(int $rawPage, int $perPage, int $total)
    {
        $this->perPage = max(1, $perPage);
        $this->total   = max(0, $total);
        $this->totalPages = (int)max(1, (int)ceil($this->total / $this->perPage));

        // Clamp page ve [1, totalPages].
        $page = $rawPage;
        if ($page < 1) {
            $page = 1;
        }
        if ($page > $this->totalPages) {
            $page = $this->totalPages;
        }
        $this->page = $page;
        $this->offset = ($this->page - 1) * $this->perPage;
    }

    public function hasPrev(): bool
    {
        return $this->page > 1;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->totalPages;
    }
}
