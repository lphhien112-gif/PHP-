<?php
namespace BookNest\Controllers;

use BookNest\Response;

class AdminController
{
    private array $books = [
        ['id' => 1, 'title' => 'Dế Mèn Phiêu Lưu Ký', 'author' => 'Tô Hoài', 'category' => 'Văn học', 'price' => 85000, 'stock' => 25, 'status' => 'Available', 'year' => 1941],
        ['id' => 2, 'title' => 'Nhà Giả Kim', 'author' => 'Paulo Coelho', 'category' => 'Tiểu thuyết', 'price' => 79000, 'stock' => 18, 'status' => 'Available', 'year' => 1988],
        ['id' => 3, 'title' => 'Đắc Nhân Tâm', 'author' => 'Dale Carnegie', 'category' => 'Kỹ năng sống', 'price' => 68000, 'stock' => 3, 'status' => 'Low stock', 'year' => 1936],
        ['id' => 4, 'title' => 'Clean Code', 'author' => 'Robert C. Martin', 'category' => 'Công nghệ', 'price' => 450000, 'stock' => 12, 'status' => 'Available', 'year' => 2008],
        ['id' => 5, 'title' => 'Tôi Tài Giỏi, Bạn Cũng Thế', 'author' => 'Adam Khoo', 'category' => 'Kỹ năng sống', 'price' => 115000, 'stock' => 0, 'status' => 'Out of stock', 'year' => 2008],
        ['id' => 6, 'title' => 'Sapiens: Lược Sử Loài Người', 'author' => 'Yuval Noah Harari', 'category' => 'Khoa học', 'price' => 199000, 'stock' => 7, 'status' => 'Available', 'year' => 2011],
        ['id' => 7, 'title' => 'Tuổi Trẻ Đáng Giá Bao Nhiêu', 'author' => 'Rosie Nguyễn', 'category' => 'Kỹ năng sống', 'price' => 76000, 'stock' => 2, 'status' => 'Low stock', 'year' => 2016],
        ['id' => 8, 'title' => 'Design Patterns', 'author' => 'Erich Gamma', 'category' => 'Công nghệ', 'price' => 520000, 'stock' => 5, 'status' => 'Available', 'year' => 1994],
        ['id' => 9, 'title' => 'Truyện Kiều', 'author' => 'Nguyễn Du', 'category' => 'Văn học', 'price' => 55000, 'stock' => 30, 'status' => 'Available', 'year' => 1820],
        ['id' => 10, 'title' => 'Atomic Habits', 'author' => 'James Clear', 'category' => 'Kỹ năng sống', 'price' => 159000, 'stock' => 0, 'status' => 'Out of stock', 'year' => 2018],
    ];

    /** GET / → Admin Dashboard */
    public function dashboard(): void
    {
        $totalBooks = count($this->books);
        $totalStock = array_sum(array_column($this->books, 'stock'));
        $outOfStock = count(array_filter($this->books, fn($b) => $b['status'] === 'Out of stock'));
        $lowStock = count(array_filter($this->books, fn($b) => $b['status'] === 'Low stock'));
        $totalValue = array_sum(array_map(fn($b) => $b['price'] * $b['stock'], $this->books));
        $categories = count(array_unique(array_column($this->books, 'category')));

        $content = Response::renderView('admin/dashboard', [
            'title' => 'Dashboard — BookNest Admin',
            'totalBooks' => $totalBooks,
            'totalStock' => $totalStock,
            'outOfStock' => $outOfStock,
            'lowStock' => $lowStock,
            'totalValue' => $totalValue,
            'categories' => $categories,
            'books' => $this->books,
            'currentPage' => 'dashboard',
        ]);
        Response::html($content);
    }

    /** GET /books → Admin book management table */
    public function books(): void
    {
        $content = Response::renderView('admin/books/index', [
            'title' => 'Quản lý sách — BookNest Admin',
            'books' => $this->books,
            'currentPage' => 'books',
        ]);
        Response::html($content);
    }

    /** GET /books/create → Admin add book form */
    public function createBook(): void
    {
        $content = Response::renderView('admin/books/create', [
            'title' => 'Thêm sách — BookNest Admin',
            'currentPage' => 'books',
        ]);
        Response::html($content);
    }

    /** POST /books → Process add book, redirect */
    public function storeBook(): void
    {
        $title  = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        if (empty($title) || empty($author)) {
            $content = Response::renderView('admin/books/create', [
                'title' => 'Thêm sách — BookNest Admin',
                'error' => 'Vui lòng điền đầy đủ tên sách và tác giả.',
                'old' => $_POST,
                'currentPage' => 'books',
            ]);
            Response::html($content, 422);
            return;
        }
        Response::redirect('/books');
    }

    /** GET /login → Admin login form */
    public function loginForm(): void
    {
        $content = Response::renderView('admin/login', [
            'title' => 'Đăng nhập Admin — BookNest',
        ]);
        Response::html($content);
    }

    /** POST /login → Process admin login */
    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if ($username === 'admin' && $password === 'admin') {
            Response::redirect('/');
            return;
        }
        $content = Response::renderView('admin/login', [
            'title' => 'Đăng nhập Admin — BookNest',
            'error' => 'Sai tài khoản hoặc mật khẩu. (Gợi ý: admin/admin)',
            'old' => ['username' => $username],
        ]);
        Response::html($content, 401);
    }

    /** GET /logout → Redirect to login */
    public function logout(): void
    {
        Response::redirect('/login');
    }
}
