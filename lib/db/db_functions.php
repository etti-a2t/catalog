<?
define('PER_PAGE', 10);
function get_connection()
{
    $settings = get_connection_settings();
    $con = mysqli_connect($settings['host'], $settings['login'], $settings['password'], $settings['db_name'], $settings['port']);
    if (!$con) {
        die("Conectare imposibila: " . mysqli_error($con));
    }
    if (mysqli_connect_errno()) {
        die("Connect failed: " . mysqli_connect_errno() . " : " . mysqli_connect_error());
    }
    return $con;
}

function get_page_data()
{

    if (isset($_GET['page'])) $page = ($_GET['page'] - 1); else $page = 0;
    $data['products'] = get_products($page, PER_PAGE);
    $data['pagination']['total'] = ceil(get_count_product() / PER_PAGE);
    $data['pagination']['page'] = $page + 1;

    prepare_response($data);
    return $data;
}

function closeConnection($c)
{
    mysqli_close($c);
}

function get_products($page, $per_page, $short = false)
{
    $start = abs($page * $per_page);

    $fields = ($short) ? 'product.id' : '*';

    if (isset($_GET['sort'])) {
        $sort_query = $_GET['sort'];
    } elseif (!empty($_POST['sort'])) {
        $sort_query = $_POST['sort'];
    } else {
        $sort_query = 'id_asc';
    }

    $cache_key = build_cache_key($page, $sort_query);
    $result = get_cache($cache_key);
    if (!$result) {
        $sort_data = get_sort($sort_query);
        $sort = $sort_data['sort'];
        $sort_field = $sort_data['sort_field'];

        if ($sort_field == 'id') {
            $q = 'select ' . $fields . ' from product JOIN (SELECT ' . $sort_field . ' FROM product ' . $sort . ' LIMIT ' . $start . ',' . $per_page . ') as b ON b.id = product.id';
        } elseif ($sort_field == 'price') {
            $q = 'select ' . $fields . ' from product JOIN (SELECT id, price FROM product ' . $sort . ' LIMIT ' . $start . ',' . $per_page . ') as b ON b.id = product.id';
        }
        $res = query($q);
    } else {
        $q = 'select ' . $fields . ' from product where id in(' . $result . ');';
        $res = query($q);
    }

    return $res;
}

function get_count_product()
{
    $c = get_connection();
    $result = get_cache('total_count');
    if (!$result) {

        $q = 'SELECT product_stat.value FROM product_stat where name= "product_count"';
        $res = mysqli_query($c, $q);
        $row = mysqli_fetch_row($res);
        if (empty($row[0])) {
            $row[0] = 0;
        }
        set_cache('total_count', $row[0]);
        return $row[0];
    }
    return $result;
}

/**
 * Main settings for connecting to the database
 * @return array
 */
function get_connection_settings()
{
    return array(
        'host' => 'localhost',
        'login' => 'root',
        'password' => 'root',
        'db_name' => 'catalog',
        'port' => 3306,
    );
}

function query($sql)
{
    $c = get_connection();
    $res = mysqli_query($c, $sql);
    $data = array();
    while ($row = mysqli_fetch_row($res)) {

        $data[] = $row;
    }
    return $data;
}

function add_product($product_data)
{
    $name = $product_data['name'];
    $description = $product_data['description'];
    $price = $product_data['price'];
    $url = $product_data['url'];

    $c = get_connection();
    $r = false;
    $sql = 'insert into product (name,  description, price, url) value("' . $name . '", "' . $description . '", ' . $price . ', "' . $url . '" )';
    return mysqli_query($c, $sql);
}

function edit_product($product_data)
{
    $name = $product_data['name'];
    $description = $product_data['description'];
    $price = $product_data['price'];
    $url = $product_data['url'];
    $id = $product_data['id'];

    $c = get_connection();
    $sql = 'update product set name="' . $name . '",  description="' . $description . '", price=' . $price . ', url="' . $url . '" where id=' . $id;
    $res = mysqli_query($c, $sql);
    return $res;
}

function remove_product()
{
    $c = get_connection();

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        return false;
    }
    $sql = 'delete from product where id=' . $id;
    $res = mysqli_query($c, $sql);
    if ($res) {
        clear_cache();
        return true;
    }
    return false;
}

function get_product()
{
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        return false;
    }
    $c = get_connection();
    $sql = 'select * from product where id=' . $id . ' limit 1';
    $res = mysqli_query($c, $sql);
    $row = mysqli_fetch_row($res);
    if (empty($row)) {
        return false;
    }
    return $row;
}

function get_sort($query)
{
    $sort = '';
    $sort_field = '';
    switch ($query) {
        case 'id_desc':
            $sort = 'ORDER BY id desc';
            $sort_field = 'id';
            break;
        case 'id_asc':
            $sort = 'ORDER BY id asc';
            $sort_field = 'id';
            break;
        case 'price_desc':
            $sort = 'ORDER BY price desc';
            $sort_field = 'price';
            break;
        case 'price_asc':
            $sort = 'ORDER BY price asc';
            $sort_field = 'price';
            break;
    }
    return array('sort' => $sort, 'sort_field' => $sort_field);
}
