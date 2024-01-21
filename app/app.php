<?php

class App
{
    private $__controller, $__action, $__params, $__routes, $__DB;
    public static $app;

    public function __construct()
    {
        global $routes;

        $this->__routes = new Route();

        self::$app = $this;

        $this->__controller = $routes['default_controller'];
        $this->__action = 'index';
        $this->__params = [];

        if (class_exists('DB')) {
            $dbObject = new DB();
            $this->__DB = $dbObject->db;
        }


        $this->handleUrl();
    }

    public function getUrl()
    {
        if (!empty($_SERVER['PATH_INFO'])) {
            $url = $_SERVER['PATH_INFO'];
        } else {
            $url = '/';
        }

        return $url;
    }

    public function handleUrl()
    {
        $url = $this->getUrl();

        $url = $this->__routes->handleUrl($url);

        // handle middleware 
        $this->globalMiddleware($this->__DB);
        $this->routeMiddleware($this->__routes->getUri(), $this->__DB);

        // handle service provider
        $this->handleAppServiceProvider($this->__DB);
        $urlArr = array_filter(explode('/', $url));
        $urlArr = array_values($urlArr);

        // handle multiple url
        $urlCheck = '';
        if (!empty($urlArr)) {
            foreach ($urlArr as $key => $item) {
                $urlCheck .= $item . '/';
                $fileCheck = rtrim($urlCheck, '/');
                $fileCheckArr = explode('/', $fileCheck);
                $fileCheckArr[$key] = ucfirst($fileCheckArr[count($fileCheckArr) - 1]);
                $fileCheck = implode('/', $fileCheckArr);

                if (!empty($urlArr[$key - 1])) {
                    unset($urlArr[$key - 1]);
                }
                if (file_exists('app/controllers/' . $fileCheck . '.php')) {
                    $urlCheck = $fileCheck;
                    break;
                }
            }

            $urlArr = array_values($urlArr);
        }

        // handle controller
        if (!empty($urlArr[0])) {
            $this->__controller = ucfirst($urlArr[0]);
        } else {
            $this->__controller = ucfirst($this->__controller);
        }

        if (empty($urlCheck)) {
            $urlCheck = $this->__controller;
        }

        if (file_exists('app/controllers/' . $urlCheck . '.php')) {
            require_once 'controllers/' . $urlCheck . '.php';

            if (class_exists($this->__controller)) {
                $this->__controller = new $this->__controller();
                unset($urlArr[0]);

                if (!empty($this->__DB)) {
                    $this->__controller->db = $this->__DB;
                }
            } else {
                $this->loadError();
            }
        } else {
            $this->loadError();
        }

        // handle action
        if (!empty($urlArr[1])) {
            $this->__action = $urlArr[1];
            unset($urlArr[1]);
        }

        // handle params, call function
        $this->__params = array_values($urlArr);

        if (method_exists($this->__controller, $this->__action)) {
            call_user_func_array([$this->__controller, $this->__action], $this->__params);
        } else {
            $this->loadError();
        }
    }

    public function loadError($name = '404', $data = [])
    {
        extract($data);
        require_once 'app/errors/' . $name . '.php';
        die();
    }

    public function routeMiddleware($uri, $db)
    {
        global $config;
        if (!empty($config['app']['routeMiddleware'])) {
            $routeMiddlewareArr = $config['app']['routeMiddleware'];
            foreach ($routeMiddlewareArr as $key => $middlewareItem) {
                if (trim($key) == trim($uri) && file_exists('app/middleware/' . $middlewareItem . '.php')) {
                    require_once 'app/middleware/' . $middlewareItem . '.php';
                    $middlewareObject = new $middlewareItem();
                    if (!empty($db)) {
                        $middlewareObject->db = $db;
                    }
                    $middlewareObject->handle();
                }
            }
        }
    }

    public function globalMiddleware($db)
    {
        global $config;
        if (!empty($config['app']['globalMiddleware'])) {
            $globalMiddlewareArr = $config['app']['globalMiddleware'];
            foreach ($globalMiddlewareArr as $middlewareItem) {
                if (file_exists('app/middleware/' . $middlewareItem . '.php')) {
                    require_once 'app/middleware/' . $middlewareItem . '.php';
                    $middlewareObject = new $middlewareItem();
                    if (!empty($db)) {
                        $middlewareObject->db = $db;
                    }
                    $middlewareObject->handle();
                }
            }
        }
    }

    public function handleAppServiceProvider($db)
    {
        global $config;
        if (!empty($config['app']['boot'])) {
            $serviceProviderArr = $config['app']['boot'];
            foreach ($serviceProviderArr as $serviceName) {
                if (file_exists('app/core/' . $serviceName . '.php')) {
                    require_once 'app/core/' . $serviceName . '.php';
                    $serviceObject = new $serviceName();
                    if (!empty($db)) {
                        $serviceObject->db = $db;
                    }
                    $serviceObject->boot();
                }
            }
        }
    }
}
