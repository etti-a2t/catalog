<?
function getViewContent($header, $content, $pagination, $metadata)
{

    $html_header = get_html_header($header);
    $html_content = get_html_content($content, $pagination, $metadata);
    return '
            <div>
               ' . $html_header . '
               ' . $html_content . '
            </div>
        ';
}

function get_view_pagination($pagination)
{
    $page = $pagination['page'];
    $total = $pagination['total'];
    $str = '<ul class="right pagination">
    <li><a href="index.php?' . get_url_with_param(array('page' => 1)) . '" class="first">First</a></li>';
    $maxPage = ($m = $page + 2) > $total ? $total : $m;
    for ($i = $page - 2; $i <= $maxPage; $i++) {
        if ($i > 0) {
            $str .= '<li ' . (($i == $page) ? 'class="active"' : '') . '><a href="index.php?' . get_url_with_param(array('page' => $i)) . '">' . $i . '</a></li>';
        }
    }

    $str .= '<li><a href="index.php?' . get_url_with_param(array('page' => $total)) . '" class="last">Last</a></li></ul>';
    return $str;
}


/**
 * @param $name
 * @return string
 */
function get_html_header($name)
{
    return '<script src="js/cache_manager.js"></script><h1>' . $name . '</h1>';
}

function get_html_content($content, $pagination, $metadata)
{


    $html = get_view_sort();
    $html .= ' <a href="add.php" id="add_product" class="btn btn-primary"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить товар</a><br><br><table class="table table-hover">';
    $html .= '<thead><tr>';
    $html .= '<th>id</th>';
    $html .= '<th>Название</th>';
    $html .= '<th>Описание</th>';
    $html .= '<th>Цена</th>';
    $html .= '<th>Изображение</th>';
    $html .= '<th></th>';
    $html .= '<th></th>';
    $html .= '</tr></thead><tbody>';
    foreach ($content as $id => $row) {

        $html .= '<tr id="' . $row[0] . '">';
        $html .= '<td>';
        $html .= ($pagination['page'] - 1) * 10 + $id + 1;
        $html .= '</td>';
        $html .= '<td>';
        $html .= $row[1];
        $html .= '</td>';
        $html .= '<td>';
        $html .= $row[2];
        $html .= '</td>';
        $html .= '<td>';
        $html .= $row[3];
        $html .= '</td>';
        $html .= '<td>';
        $html .= $row[4];
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<a href="edit.php?id=' . $row[0] . '"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<a class="link" href="#" data-artid="' . $row[0] . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
        $html .= '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '<div><form action="save.php" class="form-horizontal" id="form" role="form">

  <div class="form-group"><div class="col-sm-offset-1"><a href="index.php"  class="btn btn-primary"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Список товаров</a><br><br></div></div>
    <input type="hidden" name="page" hidden="true" value="' . $metadata['page'] . '">
    <input type="hidden" name="sort" hidden="true" value="' . $metadata['sort'] . '">
    <input type="hidden" name="total" hidden="true" value="' . $pagination['total'] . '">



</form></div>';
    $html .= get_view_pagination($pagination);;
    return $html;
}

function get_view_sort()
{

    $sort_html = '    <div class="right"><label for="sort">Сортировка:</label>
<div class="btn-group">
  <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
    id <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?' . get_url_with_param(array('sort' => 'id_asc')) . '">По возрастанию</a></li>
      <li><a href="index.php?' . get_url_with_param(array('sort' => 'id_desc')) . '">По убыванию</a></li>
    </ul>
  </div>
  <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
    Цена<span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?' . get_url_with_param(array('sort' => 'price_asc')) . '">По возрастанию</a></li>
      <li><a href="index.php?' . get_url_with_param(array('sort' => 'price_desc')) . '">По убыванию</a></li>
    </ul>
  </div>
</div></div>';
    return $sort_html;
}

function get_url_with_param($param)
{
    return http_build_query(array_merge($_GET, $param));
}

function get_edit_form($data = array())
{
    return '<div>
    <div></div>
    <div><form action="save.php" class="form-horizontal" id="form" role="form">

  <div class="form-group"><div class="col-sm-offset-1"><a href="index.php"  class="btn btn-primary"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Список товаров</a><br><br></div></div>
    <input type="hidden" name="action" hidden="true" value="' . ((empty($data)) ? 'add' : 'edit') . '">
    <input type="hidden" name="id" hidden="true" value="' . ((empty($data[0])) ? '' : $data[0]) . '">
  <div class="form-group" id="name">
    <label class="control-label col-sm-2" for="name">Название:</label>
    <div class="col-sm-10">
      <input class="form-control" name="name" placeholder="" value="' . ((isset($data[1])) ? ' ' . $data[1] : '') . '">
    </div>
  </div>
  <div class="form-group" id="description">
    <label class="control-label col-sm-2" for="description">Описание:</label>
    <div class="col-sm-10">
      <textarea class="form-control" name="description"   rows="6" placeholder="Добавьте описание">' . ((isset($data[1])) ? $data[2] : '') . '</textarea>
   <label  hidden="false">Woohoo!</label>
    </div>
  </div>
  <div class="form-group" id="price">
    <label class="control-label col-sm-2" for="price">Цена:</label>
    <div class="col-sm-10">
      <input class="form-control" name="price" placeholder="Укажите цену" value="' . ((isset($data[1])) ? ' ' . $data[3] : '') . '">
    </div>
  </div>
  <div class="form-group" id="url">
    <label class="control-label col-sm-2" for="url">url:</label>
    <div class="col-sm-10">
      <input class="form-control" name="url" placeholder="" value="' . ((isset($data[1])) ? ' ' . $data[4] : '') . '">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button id="save_button" type="submit" class="btn btn-default">Сохранить</button>
    </div>
  </div>
</form></div></div>';


}

function build_view($content)
{
    return '<!DOCTYPE html>
    <html>
    <head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    </head>
<script>
</script>
    <body>
    <div class="panel panel-default"    >
        <div class="panel-body">
            <div class="container">'
    . $content .
    '</div>
</div>
</div>
</body>
</html>';
}