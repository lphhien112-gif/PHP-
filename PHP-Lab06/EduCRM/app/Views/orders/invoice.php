<?php
/** EduCRM - F10: hoa don in duoc cho mot phieu hoc phi. */
$money = fn($n) => number_format((float)$n, 0, ',', '.');
$st = (string) $order['status'];
?>
<div class="inv-toolbar">
    <a href="/orders" class="inv-btn">&laquo; Quay lai</a>
    <button type="button" class="inv-btn primary" onclick="window.print()">In hoa don</button>
</div>

<div class="invoice">
    <div class="inv-head">
        <div class="inv-brand">EduCRM<small>Training Center CRM</small></div>
        <div class="inv-title">
            <h1>Hoa don</h1>
            <div class="code"><?= e($order['order_code']) ?></div>
        </div>
    </div>

    <div class="inv-grid">
        <div class="inv-block">
            <h3>Hoc vien</h3>
            <p><strong><?= e($order['lead_name']) ?></strong></p>
            <p><?= e($order['lead_email']) ?></p>
            <p><?= e($order['lead_phone']) ?></p>
        </div>
        <div class="inv-block" style="text-align:right;">
            <h3>Thong tin phieu</h3>
            <p>Ngay tao: <?= e(date('d/m/Y', strtotime((string)$order['created_at']))) ?></p>
            <p>Ngay thanh toan: <?= $order['paid_at'] ? e(date('d/m/Y', strtotime((string)$order['paid_at']))) : '--' ?></p>
            <p>Trang thai: <span class="pill <?= e($st) ?>"><?= e($st) ?></span></p>
        </div>
    </div>

    <table class="inv-table">
        <thead>
            <tr>
                <th>Mo ta</th>
                <th>Khoa hoc</th>
                <th class="num">Thanh tien</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Hoc phi khoa hoc</td>
                <td><?= e($order['course']) ?></td>
                <td class="num"><?= $money($order['amount']) ?> d</td>
            </tr>
            <?php if (!empty($order['note'])): ?>
            <tr>
                <td colspan="2">Ghi chu: <?= e($order['note']) ?></td>
                <td class="num">--</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="inv-total">
        <table>
            <tr>
                <td>Tam tinh</td>
                <td class="num" style="text-align:right;"><?= $money($order['amount']) ?> d</td>
            </tr>
            <tr class="grand">
                <td>Tong cong</td>
                <td class="num" style="text-align:right;"><?= $money($order['amount']) ?> d</td>
            </tr>
        </table>
    </div>

    <div class="inv-foot">
        EduCRM - Training Center CRM &middot; Hoa don nay duoc tao tu he thong &middot;
        <?= e(date('d/m/Y H:i')) ?>
    </div>
</div>
