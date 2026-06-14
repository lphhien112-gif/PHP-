<?php

namespace WorkshopHub\Support;

/**
 * WorkshopHub — JSON file storage
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien  —  MSSV: 22110059
 * Mon hoc   : Lap trinh Web      —  GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 * Doc/ghi du lieu vao file JSON trong storage/ (Lab04 khong dung DB).
 * Ghi co khoa file (LOCK_EX) de tranh ghi chong.
 */
class Storage
{
    private string $file;

    public function __construct(string $fileName)
    {
        $this->file = __DIR__ . '/../../storage/' . $fileName;
    }

    /** Doc toan bo ban ghi (mang). */
    public function all(): array
    {
        if (!file_exists($this->file)) {
            return [];
        }
        $raw = file_get_contents($this->file);
        $data = json_decode($raw ?: '[]', true);
        return is_array($data) ? $data : [];
    }

    /** So luong ban ghi (phuc vu test PRG dem dong JSON). */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * Them mot ban ghi moi voi id tu tang.
     * Tra ve ban ghi da luu (kem id, created_at).
     */
    public function insert(array $record): array
    {
        $rows = $this->all();
        $nextId = 1;
        foreach ($rows as $row) {
            if (($row['id'] ?? 0) >= $nextId) {
                $nextId = $row['id'] + 1;
            }
        }
        $record['id'] = $nextId;
        if (!isset($record['created_at'])) {
            $record['created_at'] = date('Y-m-d H:i:s');
        }
        $rows[] = $record;
        $this->save($rows);
        return $record;
    }

    /** Ghi toan bo mang xuong file (atomic-ish voi LOCK_EX). */
    private function save(array $rows): void
    {
        file_put_contents(
            $this->file,
            json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }
}
