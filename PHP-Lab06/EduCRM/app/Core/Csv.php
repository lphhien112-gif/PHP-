<?php

namespace App\Core;

/**
 * EduCRM - Csv (F4 - Export an toan)
 *
 * Stream CSV xuong trinh duyet voi:
 *  - Header Content-Disposition: attachment (tai ve, khong hien inline)
 *  - UTF-8 BOM (Excel mo dung tieng Viet, khong loi font)
 *  - Chong CSV injection: o nao bat dau bang = + - @ (hoac tab/CR) se duoc
 *    prefix dau nháy don (') de Excel/Sheets KHONG dien giai thanh cong thuc.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class Csv
{
    /**
     * Stream toan bo file CSV ra output roi exit.
     *
     * @param string         $filename ten file tai ve (vd 'leads.csv')
     * @param array<string>  $headers  dong tieu de
     * @param iterable<array> $rows     cac dong du lieu (mang gia tri)
     */
    public static function stream(string $filename, array $headers, iterable $rows): void
    {
        // Don sach output buffer de khong lan HTML vao file CSV.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Ten file an toan (chi cho chu/so/.-_).
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'export.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $safe . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');

        // BOM UTF-8 -> Excel nhan dien dung encoding.
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, array_map([self::class, 'sanitizeCell'], $headers));
        foreach ($rows as $row) {
            fputcsv($out, array_map([self::class, 'sanitizeCell'], array_values($row)));
        }

        fclose($out);
        exit;
    }

    /**
     * Chong formula injection: prefix ' neu o bat dau bang ky tu nguy hiem.
     */
    public static function sanitizeCell($value): string
    {
        $s = (string) $value;
        if ($s === '') {
            return $s;
        }
        // Bo khoang trang/tab/xuong dong dau dong truoc khi xet ky tu nguy hiem,
        // de tranh ne tranh kieu "  =CMD()" hay "\n=CMD()".
        $trimmed = ltrim($s);
        if ($trimmed === '') {
            return $s;
        }
        $first = $trimmed[0];
        if (in_array($first, ['=', '+', '-', '@', "\t", "\r", "\n"], true)) {
            return "'" . $s;
        }
        return $s;
    }
}
