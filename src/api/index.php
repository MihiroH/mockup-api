<?php

require 'app/Controllers/StoreController.php';

use App\Controllers\StoreController;

$store_controller = new StoreController;

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($request_uri, PHP_URL_PATH);
$route = str_replace('/api/', '', $path);
$route = preg_replace('/\/$/', '', $route);
$res = [];

if ($route === 'stores') {
    if ($request_method !== 'GET') {
        $res['code'] = 405;
        $res['message'] = 'Request method is not allowed. Use the GET method.';
        http_response_code($res['code']);
        echo json_encode($res);
        exit;
    }

    $pref_code = isset($_GET['pref_code']) ? $_GET['pref_code'] : null;
    $region_code = isset($_GET['region_code']) ? $_GET['region_code'] : null;

    if (!$pref_type || gettype($pref_code) !== 'integer') {
        $res['code'] = 400;
        $res['message'] += '- Request parameters are not correct.' . PHP_EOL;
        $res['message'] += 'pref_code is required that type is number.' . PHP_EOL;
    }
    if ($region_type && gettype($region_code) !== 'integer') {
        $res['code'] = 400;
        $res['message'] += '- Request parameters are not correct.' . PHP_EOL;
        $res['message'] += 'region_code is required that type is number.' . PHP_EOL;
    }
    if (isset($res['code']) && isset($res['message'])) {
        http_response_code($res['code']);
        echo json_encode($res);
        exit;
    }

    echo $store_controller->get_stores($pref_code, $region_code);
}

if ($route === 'areas') {
    if ($request_method !== 'GET') {
        $res['code'] = 405;
        $res['message'] = 'Request method is not allowed. Use the GET method.';

        http_response_code($res['code']);
        echo json_encode($res);
        exit;
    }

    echo $store_controller->get_areas();
}

