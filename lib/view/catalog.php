<?
function getViewContent($header, $content, $pagination)
{

    $pagination_html = getViewPagination($pagination);
    $sort_html = getViewSort();
    return '<!DOCTYPE html>
<html>
<head>
    <head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>

<body>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="container">
            <div>
               ' . $header . '
               ' . $sort_html . '
               ' . $content . '
               ' . $pagination_html . '
            </div>
        </div>
    </div>
</div>
</body>
</html>';
}

function getViewPagination($pagination)
{
    $page = $pagination['page'];
    $total = $pagination['total'];
    $str = '<ul class="right pagination">
    <li><a href="index.php?'.getUrlWithParam(array('page'=>1)).'" class="first">First</a></li>';
    $maxPage = ($m = $page + 2) > $total ? $total : $m;
    for ($i = $page - 2; $i <= $maxPage; $i++) {
        if ($i > 0) {
            $str .= '<li ' . (($i == $page) ? 'class="active"' : '').'><a href="index.php?'.getUrlWithParam(array('page'=>$i)).'">' . $i . '</a></li>';
        }
    }

    $str .= '<li><a href="index.php?'.getUrlWithParam(array('page'=>$total)).'" class="last">Last</a></li></ul>';
    return $str;
}


/**
 * @param $name
 * @return string
 */
function get_html_header($name)
{
    return "<h1>$name</h1>";
}

function get_html_content($content)
{
    $html = '<table class="table table-hover">';
    $html .= '<thead><tr>';
    $html .= '<th>id<th>';
    $html .= '<th>Название</th>';
    $html .= '<th>Описание</th>';
    $html .= '<th>Цена</th>';
    $html .= '<th>Изображение</th>';
    $html .= '</tr></thead><tbody>';
    foreach ($content as $row) {

        $html .= '<tr>';
        $html .= '<td>';
        $html .= $row[0];
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
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';
    return $html;
}

function getViewSort(){

    $sort_html = '    <div class="right"><label for="sort">Сортировка:</label>
<div class="btn-group">
  <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
    id <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?'.getUrlWithParam(array('sort'=>'id_asc')).'">По возрастанию</a></li>
      <li><a href="index.php?'.getUrlWithParam(array('sort'=>'id_desc')).'">По убыванию</a></li>
    </ul>
  </div>
  <div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
    Цена<span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu">
      <li><a href="index.php?'.getUrlWithParam(array('sort'=>'price_asc')).'">По возрастанию</a></li>
      <li><a href="index.php?'.getUrlWithParam(array('sort'=>'price_desc')).'">По убыванию</a></li>
    </ul>
  </div>
</div></div>';
    return $sort_html;
}

function getUrlWithParam($param){
    return http_build_query(array_merge($_GET, $param));
}