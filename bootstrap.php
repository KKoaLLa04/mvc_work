<?php
define('_DIR_ROOT', __DIR__);

// setup WEB root
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $web_root = 'https://' . $_SERVER['HTTP_HOST'];
} else {
    $web_root = 'http://' . $_SERVER['HTTP_HOST'];
}

$folderArr = explode('\\', _DIR_ROOT);
$folder = $folderArr[array_key_last($folderArr)];

$web_root .= '/' . $folder;

define('_WEB_ROOT', $web_root);

// load config auto
$config_dir = scandir('config');
if (!empty($config_dir)) {
    foreach ($config_dir as $item) {
        if ($item != '.' && $item != '..' && file_exists('config/' . $item)) {
            require_once 'config/' . $item;
        }
    }
}

// load config app auto
if (!empty($config['app']['services'])) {
    $services = $config['app']['services'];
    foreach ($services as $key => $service) {
        if (file_exists('app/core/' . $service . '.php')) {
            require_once 'app/core/' . $service . '.php';
        }
    }
}
// load core View
require_once "core/View.php";
// load core ServiceProvider
require_once "core/ServicesProvider.php";
// load core load
require_once 'core/Load.php';
// load core middleware
require_once 'core/Middleware.php';
// load core session
require_once 'core/Session.php';
// load core helpers
require_once 'core/Helpers.php';

// auto load app helpers
$helperAll = scandir('app/helpers');
if (!empty($helperAll)) {
    foreach ($helperAll as $key => $helper) {
        if ($helper != '.' && $helper != '..' && file_exists('app/helpers/' . $helper)) {
            require_once 'app/helpers/' . $helper;
        }
    }
}

// load database
if (!empty(array_filter($config['database']))) {
    require_once 'core/Connection.php';
    require_once 'core/QueryBuilder.php';
    require_once 'core/Database.php';
    require_once 'core/DB.php';
}
require_once 'core/Model.php'; // load base model

require_once 'core/Route.php';
require_once 'app/app.php';
require_once 'core/Template.php';
require_once 'core/Controller.php';
require_once 'core/Request.php';
require_once 'core/Response.php';
