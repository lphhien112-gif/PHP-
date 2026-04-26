<?php

namespace EventHub\Controllers;

use EventHub\Helpers\ResponseHelper;
use EventHub\Helpers\Validator;
use EventHub\Support\Logger;

/**
 * EventController - Xử lý các request liên quan đến sự kiện
 * EventHub - Mini Event Booking App
 */
class EventController
{
    private Logger $logger;

    /**
     * Dữ liệu mẫu: Danh sách sự kiện
     * Trong thực tế sẽ được lưu trữ trong database
     */
    private array $events = [
        [
            'id'          => 1,
            'title'       => 'Tech Summit 2026',
            'description' => 'Hội nghị công nghệ thường niên với sự tham gia của 50+ diễn giả hàng đầu Việt Nam và quốc tế.',
            'date'        => '2026-05-15',
            'time'        => '08:00',
            'location'    => 'Trung tâm Hội nghị Quốc gia, Hà Nội',
            'category'    => 'Technology',
            'price'       => 500000,
            'capacity'    => 500,
            'booked'      => 320,
            'status'      => 'Open',
            'image'       => 'tech-summit.jpg',
            'tags'        => ['AI', 'Cloud', 'DevOps'],
        ],
        [
            'id'          => 2,
            'title'       => 'Music Festival Saigon',
            'description' => 'Lễ hội âm nhạc ngoài trời quy mô lớn với các nghệ sĩ nổi tiếng trong và ngoài nước biểu diễn.',
            'date'        => '2026-05-22',
            'time'        => '18:00',
            'location'    => 'Công viên bờ sông, TP.HCM',
            'category'    => 'Music',
            'price'       => 350000,
            'capacity'    => 2000,
            'booked'      => 1980,
            'status'      => 'Full',
            'image'       => 'music-festival.jpg',
            'tags'        => ['Live Music', 'Outdoor', 'Festival'],
        ],
        [
            'id'          => 3,
            'title'       => 'Digital Art Workshop',
            'description' => 'Workshop thực hành thiết kế đồ họa kỹ thuật số và NFT Art dành cho người mới bắt đầu.',
            'date'        => '2026-06-01',
            'time'        => '09:00',
            'location'    => 'Creative Hub, Quận 1, TP.HCM',
            'category'    => 'Art',
            'price'       => 200000,
            'capacity'    => 30,
            'booked'      => 12,
            'status'      => 'Open',
            'image'       => 'art-workshop.jpg',
            'tags'        => ['Design', 'NFT', 'Creative'],
        ],
        [
            'id'          => 4,
            'title'       => 'Business Startup Bootcamp',
            'description' => 'Chương trình đào tạo khởi nghiệp 2 ngày với mentoring 1-1 từ các founder thành công.',
            'date'        => '2026-06-10',
            'time'        => '08:30',
            'location'    => 'WeWork Bến Thành, TP.HCM',
            'category'    => 'Business',
            'price'       => 1500000,
            'capacity'    => 50,
            'booked'      => 45,
            'status'      => 'Open',
            'image'       => 'startup-bootcamp.jpg',
            'tags'        => ['Startup', 'Mentoring', 'Business'],
        ],
        [
            'id'          => 5,
            'title'       => 'Photography Masterclass',
            'description' => 'Lớp học nhiếp ảnh nâng cao với thực hành chụp hình street photography tại Hà Nội.',
            'date'        => '2026-06-20',
            'time'        => '07:00',
            'location'    => 'Phố cổ Hà Nội',
            'category'    => 'Photography',
            'price'       => 450000,
            'capacity'    => 20,
            'booked'      => 8,
            'status'      => 'Open',
            'image'       => 'photography.jpg',
            'tags'        => ['Photography', 'Street', 'Masterclass'],
        ],
    ];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * GET /events - Lấy danh sách tất cả sự kiện
     * Trả về: 200 OK
     */
    public function index(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // HEAD /events - Kiểm tra endpoint tồn tại, không trả body
        if ($method === 'HEAD') {
            $this->logger->info('HEAD /events - Kiểm tra endpoint');
            ResponseHelper::head(200, [
                'X-Total-Events' => count($this->events),
                'X-API-Version'  => '1.0',
            ]);
        }

        // OPTIONS /events - Thông báo các method được phép
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

        $this->logger->info('GET /events - Lấy danh sách sự kiện thành công', [
            'total'    => count($events),
            'category' => $category,
            'status'   => $status,
        ]);

        ResponseHelper::success($events, 'Lấy danh sách sự kiện thành công. Tổng: ' . count($events) . ' sự kiện.');
    }

    /**
     * GET /events/{id} - Lấy chi tiết 1 sự kiện
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
            $this->logger->warning("404 - Không tìm thấy sự kiện ID={$id}");
            ResponseHelper::error("Không tìm thấy sự kiện với ID={$id}.", 404);
        }

        $event['seats_left'] = $event['capacity'] - $event['booked'];

        $this->logger->info("GET /events/{$id} - Lấy chi tiết sự kiện thành công");
        ResponseHelper::success($event, "Chi tiết sự kiện '{$event['title']}'.");
    }

    /**
     * POST /bookings - Đặt vé sự kiện
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

        // Kiểm tra sự kiện tồn tại
        $event = $this->findEvent($eventId);
        if (!$event) {
            $this->logger->warning("422 - Sự kiện không tồn tại: event_id={$eventId}");
            ResponseHelper::error("Sự kiện với ID={$eventId} không tồn tại.", 422, [
                'field' => 'event_id',
            ]);
        }

        // Kiểm tra sự kiện còn chỗ
        $seatsLeft = $event['capacity'] - $event['booked'];
        if ($event['status'] === 'Full' || $quantity > $seatsLeft) {
            $this->logger->warning("422 - Sự kiện '{$event['title']}' đã hết chỗ hoặc không đủ chỗ", [
                'requested' => $quantity,
                'available' => $seatsLeft,
            ]);
            ResponseHelper::error(
                "Sự kiện '{$event['title']}' không đủ chỗ. Yêu cầu: {$quantity} vé, còn lại: {$seatsLeft} chỗ.",
                422,
                ['seats_available' => $seatsLeft, 'seats_requested' => $quantity]
            );
        }

        // Tạo booking thành công
        $booking = [
            'booking_id'      => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'event_id'        => $eventId,
            'event_title'     => $event['title'],
            'event_date'      => $event['date'],
            'event_time'      => $event['time'],
            'event_location'  => $event['location'],
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
            'event'      => $event['title'],
            'attendee'   => $booking['full_name'],
            'tickets'    => $quantity,
        ]);

        ResponseHelper::success($booking, "Đặt vé thành công! Mã đặt chỗ: {$booking['booking_id']}", 201);
    }

    /**
     * Tìm sự kiện theo ID
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
