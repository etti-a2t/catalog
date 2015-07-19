<?php
require_once __DIR__ . '/db/db_functions.php';
require_once __DIR__ . '/cache_functions.php';
require_once __DIR__ . '/view/catalog.php';
function show_list()
{
    $data = get_page_data();
    $metadata = array(
        'page' => (isset($_GET['page']) ? $_GET['page'] : 1),
        'sort' => (isset($_GET['sort']) ? $_GET['sort'] : ''),
    );
    $response = getViewContent('Список товаров', $data['products'], $data['pagination'], $metadata);
    $view = build_view($response);
    _show($view);
}

function prepare_response(&$response)
{
    if (!isset($response['header'])) {
        $response['header'] = 'Список товаров';
    }
    if (empty($response['content'])) {
        $response['content'] = 'Нет данных для отображения';
    }
}

function _show($response)
{
    print_r($response);
}

function array_to_string($array)
{
    $string = '';
    foreach ($array as &$el) {
        if (is_array($el)) {
            $el = array_to_string($el);
        }
        $string .= $el;

    }
    return $string;
}


function show_edit()
{
    $id = (empty($_GET['id'])) ? '' : $_GET['id'];
    $product = get_product($id);
    if (!empty($product)) {
        $content = get_edit_form($product);

    } else {
        $content = 'All bad';
    }

    $response = build_view($content);
    _show($response);
}

function show_add()
{
    $content = get_edit_form();
    $response = build_view($content);
    _show($response);
}

function save_product()
{
    $data = array(
        'product' => array(
            'name' => '',
            'description' => '',
            'price' => 0,
            'url' => '')
    );
    $errors = array();
    $data['success'] = false;

    if (empty($_POST['action'])) {
        $errors['action'] = 'Внутренняя ошибка';
    }
    if ($_POST['action'] == 'edit') {
        if (empty($_POST['id'])) {
            $errors['action'] = 'Внутренняя ошибка';
        } else {
            $data['product']['id'] = $_POST['id'];
        }
    }

    if (empty($_POST['name'])) {
        $errors['name'] = 'Нужно указать название товара';
    } else {
        $data['product']['name'] = $_POST['name'];
    }


    if (empty($_POST['price'])) {
        $errors['price'] = 'Нужно указать цену';
    } else {

        $data['product']['price'] = trim($_POST['price']);
        preg_match('/^(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/', $data['product']['price'], $match);
        if (empty($match)) {
            $errors['price'] = 'Цена в не правильном формате, пример: 1232.20';
        }
    }
    if (!empty($_POST['description'])) {
        $data['product']['description'] = $_POST['description'];
    }
    if (!empty($_POST['url'])) {
        $data['product']['url'] = $_POST['url'];
    }


    if (!empty($errors)) {
        $data['errors'] = $errors;
    } else {
        switch ($_POST['action']) {
            case 'edit':
                if (edit_product($data['product'])) {
                    $data['success'] = true;
                }
                break;
            case 'add':
                if (add_product($data['product'])) {
                    $data['success'] = true;
                }
                break;
        }
        clear_cache();
    }

    // return all our data to an AJAX call
    echo json_encode($data);


}
