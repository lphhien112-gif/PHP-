<?php
/** EduCRM - Flash message (hien 1 lan roi tu xoa). */
foreach (get_flash() as $f):
?>
    <div class="flash <?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endforeach; ?>
