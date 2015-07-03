<?
function getConnection()
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

function getProductsData()
{
    $c = getConnection();

    if (isset($_GET['page'])) $page = ($_GET['page'] - 1); else $page = 0;
    $per_page = 10;
    $data['products'] = getProducts($c, $page, $per_page);
    $data['pagination']['total'] = ceil(getCountProduct($c) / $per_page);
    $data['pagination']['page'] = $page + 1;

    prepare_response($data);
    return $data;
}

function closeConnection($c)
{
    mysqli_close($c);
}

function getProducts($c, $page, $per_page)
{
    if (isset($_GET['sort'])) {
        switch ($_GET['sort']) {
            case 'id_desc':
                $sort = 'ORDER BY id desc';
                break;
            case 'id_asc':
                $sort = 'ORDER BY id asc';
                break;
            case 'price_desc':
                $sort = 'ORDER BY price desc';
                break;
            case 'price_asc':
                $sort = 'ORDER BY price asc';
                break;
            default:
                $sort = '';
        }

    } else {
        $sort = '';
    }
    $start = abs($page * $per_page);

    $q = "SELECT * FROM `product` " . $sort . " LIMIT $start,$per_page";
    $res = mysqli_query($c, $q);
    $data = array();
    while ($row = mysqli_fetch_row($res)) {

        $data[] = $row;
    }
    return $data;
}

function getCountProduct($c)
{
    $q = "SELECT count(id) FROM `product`";
    $res = mysqli_query($c, $q);
    $row = mysqli_fetch_row($res);
    if (empty($row[0])) {
        $row[0] = 0;
    }
    return $row[0];
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
    $c = getConnection();
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

    $c = getConnection();
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

    $c = getConnection();
    $sql = 'update product set name="' . $name . '",  description="' . $description . '", price=' . $price . ', url="' . $url . '" where id=' . $id;
    $res = mysqli_query($c, $sql);
    return $res;
}

function remove_product()
{
    $c = getConnection();

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        return false;
    }
    $sql = 'delete from product where id=' . $id;
    $res = mysqli_query($c, $sql);
    if ($res) {
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
    $c = getConnection();
    $sql = 'select * from product where id=' . $id . ' limit 1';
    $res = mysqli_query($c, $sql);
    $row = mysqli_fetch_row($res);
    if (empty($row)) {
        return false;
    }
    return $row;
}
