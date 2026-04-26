<?php

namespace EventHub\Controllers;

use EventHub\Helpers\ResponseHelper;
use EventHub\Helpers\Validator;
use EventHub\Support\Logger;

/**
 * EventController - Xử lý các request liên quan đến suất chiếu phim
 * CineHub - Mini Movie Booking App
 */
class EventController
{
    private Logger $logger;

    /**
     * Dữ liệu mẫu: Danh sách suất chiếu phim
     * Trong thực tế sẽ được lưu trữ trong database
     */
    private array $events = [
        [
            'id'          => 1,
            'title'       => 'Stellar Frontier',
            'description' => 'Hành trình xuyên không gian đầy kịch tính khi một phi hành gia phát hiện cánh cổng dẫn đến vũ trụ song song. Liệu anh có tìm được đường về?',
            'date'        => '2026-05-15',
            'time'        => '19:30',
            'location'    => 'CGV Vincom Center, Quận 1, TP.HCM',
            'category'    => 'Sci-Fi',
            'price'       => 120000,
            'capacity'    => 120,
            'booked'      => 85,
            'status'      => 'Open',
            'image'       => 'stellar-frontier.png',
            'tags'        => ['IMAX', '3D', 'Sci-Fi'],
            'duration'    => '148 phút',
            'rating'      => '8.5',
            'director'    => 'Christopher Nolan',
        ],
        [
            'id'          => 2,
            'title'       => 'Love in Saigon',
            'description' => 'Câu chuyện tình yêu lãng mạn giữa Sài Gòn cổ kính, khi hai người xa lạ tình cờ gặp nhau trong một con hẻm nhỏ và thay đổi cuộc đời nhau mãi mãi.',
            'date'        => '2026-05-20',
            'time'        => '20:00',
            'location'    => 'Lotte Cinema Thủ Đức, TP.HCM',
            'category'    => 'Romance',
            'price'       => 95000,
            'capacity'    => 80,
            'booked'      => 80,
            'status'      => 'Full',
            'image'       => 'love-saigon.png',
            'tags'        => ['Romance', 'Việt Nam', 'Drama'],
            'duration'    => '115 phút',
            'rating'      => '7.8',
            'director'    => 'Victor Vũ',
        ],
        [
            'id'          => 3,
            'title'       => 'The Hollow',
            'description' => 'Một gia đình chuyển đến biệt thự cổ giữa rừng sâu, nơi những bóng ma từ quá khứ bắt đầu thức dậy. Không ai rời đi được sau khi trời tối.',
            'date'        => '2026-05-25',
            'time'        => '21:30',
            'location'    => 'Galaxy Cinema Nguyễn Du, TP.HCM',
            'category'    => 'Horror',
            'price'       => 110000,
            'capacity'    => 100,
            'booked'      => 42,
            'status'      => 'Open',
            'image'       => 'the-hollow.png',
            'tags'        => ['Horror', 'Thriller', '18+'],
            'duration'    => '125 phút',
            'rating'      => '7.2',
            'director'    => 'James Wan',
        ],
        [
            'id'          => 4,
            'title'       => 'Dragon Kingdom',
            'description' => 'Bộ phim hoạt hình kể về cô bé Linh cưỡi rồng vàng phiêu lưu qua những ngọn núi bay, giải cứu vương quốc kỳ diệu khỏi bóng tối.',
            'date'        => '2026-06-01',
            'time'        => '10:00',
            'location'    => 'BHD Star Phạm Hùng, Hà Nội',
            'category'    => 'Animation',
            'price'       => 85000,
            'capacity'    => 150,
            'booked'      => 67,
            'status'      => 'Open',
            'image'       => 'dragon-kingdom.png',
            'tags'        => ['Animation', 'Family', 'Fantasy'],
            'duration'    => '98 phút',
            'rating'      => '8.9',
            'director'    => 'Hayao Miyazaki',
        ],
        [
            'id'          => 5,
            'title'       => 'Shadow Warrior',
            'description' => 'Chiến binh cuối cùng của triều đại cổ đại phải đối mặt với kẻ thù hùng mạnh nhất. Hai thanh kiếm, một sứ mệnh, và trận chiến dưới mưa huyền thoại.',
            'date'        => '2026-06-10',
            'time'        => '18:00',
            'location'    => 'CGV Aeon Mall Tân Phú, TP.HCM',
            'category'    => 'Action',
            'price'       => 130000,
            'capacity'    => 200,
            'booked'      => 195,
            'status'      => 'Open',
            'image'       => 'shadow-warrior.png',
            'tags'        => ['Action', 'Martial Arts', 'Epic'],
            'duration'    => '152 phút',
            'rating'      => '9.1',
            'director'    => 'Ngô Thanh Vân',
        ],
    ];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * GET /events - Lấy danh sách tất cả suất chiếu
     * Trả về: 200 OK
     */
    public function index(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // HEAD /events
        if ($method === 'HEAD') {
            $this->logger->info('HEAD /events - Kiểm tra endpoint');
            ResponseHelper::head(200, [
                'X-Total-Events' => count($this->events),
                'X-API-Version'  => '1.0',
            ]);
        }

        // OPTIONS /events
        if ($method === 'OPTIONS') {
            $this->logger->info('OPTIONS /events - Liệt kê methods');
            ResponseHelper::options(['GET', 'POST', 'HEAD', 'OPTIONS']);
        }

        // Chỉ chấp nhận GET
        if ($method !== 'GET') {
            $this->logger->warning("405 - Sai method: {$method} tại /events");
            ResponseHelper::error(
                "Method '{$method}' không được phép. Endpoint này chỉ hỗ trợ GET.",
                405
            );
        }

        // Lọc theo category nếu có query param
        $category = $_GET['category'] ?? null;
        $status   = $_GET['status'] ?? null;

        $events = $this->events;

        if ($category) {
            $events = array_values(array_filter(
                $events,
                fn($e) => strtolower($e['category']) === strtolower($category)
            ));
        }

        if ($status) {
            $events = array_values(array_filter(
                $events,
                fn($e) => strtolower($e['status']) === strtolower($status)
            ));
        }

        // Thêm thông tin còn lại chỗ
        $events = array_map(function ($event) {
            $event['seats_left'] = $event['capacity'] - $event['booked'];
            return $event;
        }, $events);

        $this->logger->info('GET /events - Lấy danh sách phim thành công', [
            'total'    => count($events),
            'category' => $category,
            'status'   => $status,
        ]);

        ResponseHelper::success($events, 'Lấy danh sách phim thành công. Tổng: ' . count($events) . ' phim.');
    }

    /**
     * GET /events/{id} - Lấy chi tiết 1 suất chiếu
     * Trả về: 200 OK hoặc 404 Not Found
     */
    public function show(int $id): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'HEAD') {
            $event = $this->findEvent($id);
            ResponseHelper::head($event ? 200 : 404);
        }

        if ($method === 'OPTIONS') {
            ResponseHelper::options(['GET', 'HEAD', 'OPTIONS']);
        }

        if ($method !== 'GET') {
            $this->logger->warning("405 - Sai method: {$method} tại /events/{$id}");
            ResponseHelper::error("Method '{$method}' không được phép cho endpoint này.", 405);
        }

        $event = $this->findEvent($id);

        if (!$event) {
            $this->logger->warning("404 - Không tìm thấy phim ID={$id}");
            ResponseHelper::error("Không tìm thấy phim với ID={$id}.", 404);
        }

        $event['seats_left'] = $event['capacity'] - $event['booked'];

        $this->logger->info("GET /events/{$id} - Lấy chi tiết phim thành công");
        ResponseHelper::success($event, "Chi tiết phim '{$event['title']}'.");
    }

    /**
     * POST /bookings - Đặt vé xem phim
     * Trả về: 201 Created, 415, 422
     */
    public function book(): void
    {
        $method      = $_SERVER['REQUEST_METHOD'];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if ($method === 'OPTIONS') {
            ResponseHelper::options(['POST', 'OPTIONS']);
        }

        // Kiểm tra method - chỉ chấp nhận POST
        if ($method !== 'POST') {
            $this->logger->warning("405 - Sai method: {$method} tại /bookings");
            ResponseHelper::error(
                "Method '{$method}' không được phép. Endpoint /bookings chỉ hỗ trợ POST.",
                405
            );
        }

        // Kiểm tra Content-Type - phải là application/json
        if (!str_contains($contentType, 'application/json')) {
            $this->logger->warning("415 - Sai Content-Type: '{$contentType}'");
            ResponseHelper::error(
                "Content-Type không hợp lệ: '{$contentType}'. Endpoint này yêu cầu 'application/json'.",
                415,
                ['required_content_type' => 'application/json']
            );
        }

        // Đọc và parse JSON body
        $rawBody = file_get_contents('php://input');
        $body    = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->warning('422 - Body không phải JSON hợp lệ');
            ResponseHelper::error('Body không phải JSON hợp lệ: ' . json_last_error_msg(), 422);
        }

        // Validate dữ liệu
        $validator = new Validator();
        $validator
            ->required('event_id', $body['event_id'] ?? null)
            ->required('full_name', $body['full_name'] ?? '')
            ->minLength('full_name', $body['full_name'] ?? '', 3)
            ->required('email', $body['email'] ?? '')
            ->email('email', $body['email'] ?? '')
            ->required('phone', $body['phone'] ?? '')
            ->phone('phone', $body['phone'] ?? '')
            ->required('ticket_quantity', $body['ticket_quantity'] ?? null)
            ->isInteger('ticket_quantity', $body['ticket_quantity'] ?? 0)
            ->isPositive('ticket_quantity', $body['ticket_quantity'] ?? 0);

        if (!$validator->isValid()) {
            $this->logger->warning('422 - Dữ liệu đặt vé không hợp lệ', $validator->getErrors());
            ResponseHelper::error('Dữ liệu gửi lên không hợp lệ.', 422, [
                'validation_errors' => $validator->getErrors(),
            ]);
        }

        $eventId  = (int) $body['event_id'];
        $quantity = (int) $body['ticket_quantity'];

        // Kiểm tra phim tồn tại
        $event = $this->findEvent($eventId);
        if (!$event) {
            $this->logger->warning("422 - Phim không tồn tại: event_id={$eventId}");
            ResponseHelper::error("Suất chiếu với ID={$eventId} không tồn tại.", 422, [
                'field' => 'event_id',
            ]);
        }

        // Kiểm tra còn chỗ
        $seatsLeft = $event['capacity'] - $event['booked'];
        if ($event['status'] === 'Full' || $quantity > $seatsLeft) {
            $this->logger->warning("422 - Phim '{$event['title']}' hết chỗ", [
                'requested' => $quantity,
                'available' => $seatsLeft,
            ]);
            ResponseHelper::error(
                "Suất chiếu '{$event['title']}' không đủ ghế. Yêu cầu: {$quantity} vé, còn lại: {$seatsLeft} ghế.",
                422,
                ['seats_available' => $seatsLeft, 'seats_requested' => $quantity]
            );
        }

        // Tạo booking thành công
        $booking = [
            'booking_id'      => 'TK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'event_id'        => $eventId,
            'movie_title'     => $event['title'],
            'showtime'        => $event['date'] . ' ' . $event['time'],
            'cinema'          => $event['location'],
            'full_name'       => htmlspecialchars(trim($body['full_name'])),
            'email'           => strtolower(trim($body['email'])),
            'phone'           => trim($body['phone']),
            'ticket_quantity' => $quantity,
            'total_price'     => $event['price'] * $quantity,
            'booked_at'       => date('Y-m-d H:i:s'),
            'status'          => 'Confirmed',
        ];

        $this->logger->info('201 - Đặt vé thành công', [
            'booking_id' => $booking['booking_id'],
            'movie'      => $event['title'],
            'attendee'   => $booking['full_name'],
            'tickets'    => $quantity,
        ]);

        ResponseHelper::success($booking, "Đặt vé thành công! Mã vé: {$booking['booking_id']}", 201);
    }

    /**
     * Tìm phim theo ID
     */
    private function findEvent(int $id): ?array
    {
        foreach ($this->events as $event) {
            if ($event['id'] === $id) {
                return $event;
            }
        }
        return null;
    }
}
