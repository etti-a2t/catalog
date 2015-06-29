<?php
require_once __DIR__ . '/db/db_functions.php';
require_once __DIR__ . '/view/catalog.php';
function show()
{
    $data = getProductsData();
    $header = 'Список товаров';
    $content= $data['products'];
    $pagination = $data['pagination'];
    prepare_response($response);
    $html_header = get_html_header($header);
    $html_content = get_html_content($content);
    $html_response = array('header' => $html_header, 'content' => $html_content);

    _show($html_response, $pagination);
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

function _show($response, $pagination)
{
    $response = getViewContent($response['header'], $response['content'], $pagination);

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

function generator($data)
{
    $c = getConnection();

    $params = '';
    foreach ($data as $el) {
        $params .= '("' . addslashes($el['name']) . '", ' . '"' . addslashes($el['description']) . '", ' . $el['price'] . '), ';
    }
    $params = substr($params, 0, -2);


    $query = "INSERT INTO product (name, description, price) VALUES " . $params;

    if (mysqli_query($c, $query)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($c);
    }

    closeConnection($c);
}