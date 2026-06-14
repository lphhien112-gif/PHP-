<?php

namespace ClinicDesk\Core;

/**
 * View - Render file PHP trong app/Views ra chuoi HTML (output buffering).
 *
 * View KHONG chua SQL. Du lieu da duoc Controller chuan bi va truyen vao qua $data.
 */
class View
{
    /**
     * Render mot view va tra ve chuoi HTML.
     *
     * @param string $name Ten view khong co .php, vi du 'patients/index'
     * @param array  $data Bien truyen vao view (extract thanh bien rieng)
     */
    public static function render(string $name, array $data = []): string
    {
        extract($data, EXTR_SKIP);

        $viewPath = __DIR__ . '/../Views/' . $name . '.php';
        if (!file_exists($viewPath)) {
            return '<h1>View not found: ' . htmlspecialchars($name) . '</h1>';
        }

        ob_start();
        require $viewPath;
        return ob_get_clean();
    }

    /**
     * Helper escape HTML, dung trong view: <?= View::e($x) ?>
     */
    public static function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
