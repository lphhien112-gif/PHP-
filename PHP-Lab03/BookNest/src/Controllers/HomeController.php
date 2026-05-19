<?php

namespace BookNest\Controllers;

use BookNest\Response;

/**
 * HomeController
 * 
 * Xử lý trang chủ BookNest
 */
class HomeController
{
    /**
     * GET / → Trang chủ HTML
     */
    public function index(): void
    {
        $data = [
            'title'      => 'BookNest — Hiệu Sách Trực Tuyến',
            'totalBooks'  => 10,
            'categories'  => 5,
            'totalAuthors' => 10,
        ];

        $content = Response::renderView('home', $data);
        Response::html($content);
    }
}
