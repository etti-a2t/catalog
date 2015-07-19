<?php
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/cache_functions.php';

$current_page = (int)$_POST['page'] - 1;
$total = (int)$_POST['total'] - 1;
$per_page = PER_PAGE;
$sort = (!empty($_POST['sort'])) ? $_POST['sort'] : 'id_asc';
$start = ($current_page - 2 <= 0) ? 0 : $current_page - 2;
$finish = ($current_page + 2 >= $total - 1) ? $total : $current_page + 2;
if ($total > 0) {
    save_page_data(0, $sort, $per_page);
    save_page_data($total, $sort, $per_page);
}
for ($i = $start; $i <= $finish; $i++) {
    save_page_data($i, $sort, $per_page);
}

echo json_encode(array(
    'success' => true,
    'error' => true,
    'message' => true
));
