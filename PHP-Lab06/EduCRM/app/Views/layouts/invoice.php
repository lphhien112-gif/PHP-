<?php
/**
 * EduCRM - Layout hoa don in (F10).
 * Khong sidebar/topbar. CSS in rieng (print CSS): an cac nut khi @media print.
 * Bien nhan vao: $title, $content.
 */
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Hoa don') ?> - EduCRM</title>
    <link rel="icon" type="image/png" href="/assets/img/favicon-32.png">
    <style>
        :root { --brand:#4f46e5; --ink:#111827; --muted:#6b7280; --border:#e5e7eb; }
        * { box-sizing: border-box; }
        body {
            margin: 0; background: #f6f7fb; color: var(--ink);
            font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .inv-toolbar {
            max-width: 800px; margin: 18px auto 0; padding: 0 20px;
            display: flex; gap: 10px; justify-content: flex-end;
        }
        .inv-btn {
            border: 1px solid var(--border); background:#fff; color: var(--ink);
            padding: 9px 16px; border-radius: 8px; font-size: 14px; cursor: pointer;
            text-decoration: none; display: inline-block;
        }
        .inv-btn.primary { background: var(--brand); color:#fff; border-color: var(--brand); }
        .invoice {
            max-width: 800px; margin: 14px auto 40px; background:#fff;
            border: 1px solid var(--border); border-radius: 12px; padding: 40px 44px;
            box-shadow: 0 8px 24px rgba(16,24,40,.08);
        }
        .inv-head { display:flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--brand); padding-bottom: 18px; margin-bottom: 24px; }
        .inv-brand { font-size: 24px; font-weight: 800; color: var(--brand); letter-spacing:.3px; }
        .inv-brand small { display:block; font-size: 12px; font-weight: 500; color: var(--muted); letter-spacing: 0; }
        .inv-title { text-align: right; }
        .inv-title h1 { margin: 0; font-size: 26px; letter-spacing: 2px; text-transform: uppercase; }
        .inv-title .code { color: var(--muted); font-size: 14px; margin-top: 4px; }
        .inv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
        .inv-block h3 { margin: 0 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); }
        .inv-block p { margin: 2px 0; font-size: 14px; }
        table.inv-table { width:100%; border-collapse: collapse; margin-bottom: 22px; }
        table.inv-table th, table.inv-table td { text-align: left; padding: 12px 14px; font-size: 14px; }
        table.inv-table thead th { background:#f3f4f6; border-bottom: 1px solid var(--border); }
        table.inv-table td.num, table.inv-table th.num { text-align: right; }
        table.inv-table tbody td { border-bottom: 1px solid var(--border); }
        .inv-total { display:flex; justify-content: flex-end; }
        .inv-total table { width: 280px; border-collapse: collapse; }
        .inv-total td { padding: 8px 14px; font-size: 14px; }
        .inv-total .grand td { border-top: 2px solid var(--brand); font-size: 18px; font-weight: 800; }
        .pill { display:inline-block; padding: 3px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .pill.paid { background:#dcfce7; color:#166534; }
        .pill.pending { background:#fef3c7; color:#92400e; }
        .pill.refunded { background:#e0f2fe; color:#075985; }
        .pill.cancelled { background:#fee2e2; color:#991b1b; }
        .inv-foot { margin-top: 30px; padding-top: 16px; border-top: 1px dashed var(--border); color: var(--muted); font-size: 12px; text-align: center; }
        @media print {
            body { background:#fff; }
            .inv-toolbar { display: none !important; }
            .invoice { box-shadow: none; border: none; margin: 0; max-width: none; padding: 0; }
        }
    </style>
</head>
<body>
    <?= $content ?>
</body>
</html>
