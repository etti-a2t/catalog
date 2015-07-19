<?php
function set_cache($key, $value)
{
    $c = get_memcache_connect();
    memcache_set($c, $key, $value);
}

/**
 * @param $key
 * @return mixed value by key
 */
function get_cache($key)
{
    $c = get_memcache_connect();
    return memcache_get($c, $key);
}

function clear_cache()
{
    memcache_flush(get_memcache_connect());
}

/**
 * @return Resource|boolean
 */
function get_memcache_connect()
{
    return memcache_connect('localhost', 11211);
}

function build_cache_key($page, $sort)
{
    return $page . '_' . $sort;
}

function save_page_data($page, $sort, $per_page){
    $cache_key = build_cache_key($page, $sort);
    $result = get_cache($cache_key);
    if (!$result) {
        $data = get_products($page, $per_page, true);
        $ids = '';
        foreach ($data as $product) {
            $ids .= $product[0] . ',';
        }
        set_cache($cache_key, substr($ids, 0, -1));
    }
}