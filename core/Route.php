<?php

class Route
{
    private $__uri;
    public function handleUrl($url)
    {
        global $routes;

        unset($routes['default_controller']);

        $url = trim($url, '/');

        $handleUrl = $url;
        if (!empty($routes)) {
            foreach ($routes as $key => $item) {
                if (preg_match('~^' . $key . '$~is', $handleUrl)) {
                    $handleUrl = preg_replace('~^' . $key . '$~is', $item, $url);
                    $this->__uri = $key;
                }
            }
        }

        return $handleUrl;
    }

    public function getUri()
    {
        return $this->__uri;
    }

    public static function getFullUrl()
    {
        $uri = App::$app->getUrl();
        return _WEB_ROOT . $uri;
    }
}
