<?php

namespace EventHub\Controllers;

use EventHub\Helpers\ResponseHelper;

/**
 * HomeController - Trang chủ EventHub
 */
class HomeController
{
    public function index(): void
    {
        $html = file_get_contents(dirname(__DIR__, 2) . '/views/home.html');
        ResponseHelper::html($html, 200);
    }
}
