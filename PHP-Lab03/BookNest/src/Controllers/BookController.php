<?php

namespace BookNest\Controllers;

use BookNest\Response;

/**
 * BookController
 * 
 * Quản lý sách: danh sách, form tạo mới, xử lý submit
 */
class BookController
{
    /**
     * Dữ liệu mẫu 10 cuốn sách
     */
    private array $books = [
        [
            'id'       => 1,
            'title'    => 'Dế Mèn Phiêu Lưu Ký',
            'author'   => 'Tô Hoài',
            'category' => 'Văn học',
            'price'    => 85000,
            'stock'    => 25,
            'status'   => 'Available',
            'year'     => 1941,
            'image'    => 'book-01.png',
            'desc'     => 'Cuộc phiêu lưu của chú dế mèn qua thế giới côn trùng.',
        ],
        [
            'id'       => 2,
            'title'    => 'Nhà Giả Kim',
            'author'   => 'Paulo Coelho',
            'category' => 'Tiểu thuyết',
            'price'    => 79000,
            'stock'    => 18,
            'status'   => 'Available',
            'year'     => 1988,
            'image'    => 'book-02.png',
            'desc'     => 'Hành trình tìm kiếm kho báu và ước mơ đời người.',
        ],
        [
            'id'       => 3,
            'title'    => 'Đắc Nhân Tâm',
            'author'   => 'Dale Carnegie',
            'category' => 'Kỹ năng sống',
            'price'    => 68000,
            'stock'    => 3,
            'status'   => 'Low stock',
            'year'     => 1936,
            'image'    => 'book-03.png',
            'desc'     => 'Nghệ thuật giao tiếp và ứng xử trong cuộc sống.',
        ],
        [
            'id'       => 4,
            'title'    => 'Clean Code',
            'author'   => 'Robert C. Martin',
            'category' => 'Công nghệ',
            'price'    => 450000,
            'stock'    => 12,
            'status'   => 'Available',
            'year'     => 2008,
            'image'    => 'book-04.png',
            'desc'     => 'Viết code sạch, dễ đọc và dễ bảo trì.',
        ],
        [
            'id'       => 5,
            'title'    => 'Tôi Tài Giỏi, Bạn Cũng Thế',
            'author'   => 'Adam Khoo',
            'category' => 'Kỹ năng sống',
            'price'    => 115000,
            'stock'    => 0,
            'status'   => 'Out of stock',
            'year'     => 2008,
            'image'    => 'book-05.png',
            'desc'     => 'Phương pháp học tập hiệu quả cho mọi lứa tuổi.',
        ],
        [
            'id'       => 6,
            'title'    => 'Sapiens: Lược Sử Loài Người',
            'author'   => 'Yuval Noah Harari',
            'category' => 'Khoa học',
            'price'    => 199000,
            'stock'    => 7,
            'status'   => 'Available',
            'year'     => 2011,
            'image'    => 'book-06.png',
            'desc'     => 'Lịch sử 70.000 năm phát triển của loài người.',
        ],
        [
            'id'       => 7,
            'title'    => 'Tuổi Trẻ Đáng Giá Bao Nhiêu',
            'author'   => 'Rosie Nguyễn',
            'category' => 'Kỹ năng sống',
            'price'    => 76000,
            'stock'    => 2,
            'status'   => 'Low stock',
            'year'     => 2016,
            'image'    => 'book-07.png',
            'desc'     => 'Sống trọn vẹn tuổi trẻ với đam mê và mục tiêu.',
        ],
        [
            'id'       => 8,
            'title'    => 'Design Patterns',
            'author'   => 'Erich Gamma',
            'category' => 'Công nghệ',
            'price'    => 520000,
            'stock'    => 5,
            'status'   => 'Available',
            'year'     => 1994,
            'image'    => 'book-08.png',
            'desc'     => 'Các mẫu thiết kế kinh điển trong lập trình OOP.',
        ],
        [
            'id'       => 9,
            'title'    => 'Truyện Kiều',
            'author'   => 'Nguyễn Du',
            'category' => 'Văn học',
            'price'    => 55000,
            'stock'    => 30,
            'status'   => 'Available',
            'year'     => 1820,
            'image'    => 'book-09.png',
            'desc'     => 'Kiệt tác văn học cổ điển Việt Nam bằng thơ lục bát.',
        ],
        [
            'id'       => 10,
            'title'    => 'Atomic Habits',
            'author'   => 'James Clear',
            'category' => 'Kỹ năng sống',
            'price'    => 159000,
            'stock'    => 0,
            'status'   => 'Out of stock',
            'year'     => 2018,
            'image'    => 'book-10.png',
            'desc'     => 'Thay đổi tí hon, kết quả phi thường.',
        ],
    ];

    /**
     * GET /books → Danh sách sách (200 OK, HTML)
     */
    public function index(): void
    {
        $content = Response::renderView('books/index', [
            'title' => 'Danh sách sách — BookNest',
            'books' => $this->books,
        ]);
        Response::html($content);
    }

    /**
     * GET /books/create → Form thêm sách mới (200 OK, HTML)
     */
    public function create(): void
    {
        $content = Response::renderView('books/create', [
            'title' => 'Thêm sách mới — BookNest',
        ]);
        Response::html($content);
    }

    /**
     * POST /books → Xử lý submit form thêm sách
     * Redirect về /books (302 Found)
     */
    public function store(): void
    {
        // Lấy dữ liệu từ form POST
        $title    = $_POST['title'] ?? '';
        $author   = $_POST['author'] ?? '';
        $category = $_POST['category'] ?? '';
        $price    = $_POST['price'] ?? 0;
        $stock    = $_POST['stock'] ?? 0;

        // Validation cơ bản
        if (empty($title) || empty($author)) {
            $content = Response::renderView('books/create', [
                'title'   => 'Thêm sách mới — BookNest',
                'error'   => 'Vui lòng điền đầy đủ tên sách và tác giả.',
                'old'     => $_POST,
            ]);
            Response::html($content, 422);
            return;
        }

        // Trong thực tế sẽ lưu vào database
        // Hiện tại chỉ demo redirect về danh sách
        Response::redirect('/books');
    }
}
