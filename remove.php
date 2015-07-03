<?php
require_once __DIR__ . '/lib/functions.php';

//die;
$res = remove_product();
if ($res) {
    $message = 'Продукт с id:' . $_GET['id'] . ' успешно удален';
    $success = true;
} else {
    $message = 'Не удалось удалить продукт id:' . $_GET['id'];
    $success = false;

}
echo json_encode(array('success' => $success, 'message' => $message));
