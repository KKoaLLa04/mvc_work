<?php

class Session
{

    public static function data($key = '', $value = '')
    {
        $sessionKey = self::isInvalid();

        if (!empty($sessionKey)) {
            if (!empty($value)) {
                if (!empty($key)) {
                    $_SESSION[$sessionKey][$key] = $value;
                    return true;
                }

                return false;
            } else {
                if (!empty($_SESSION[$sessionKey])) {
                    if (empty($key) && !empty($_SESSION)) {
                        return $_SESSION[$sessionKey];
                    } else {
                        if (!empty($_SESSION[$sessionKey][$key])) {
                            return $_SESSION[$sessionKey][$key];
                        }
                        return false;
                    }
                }
            }
        }

        return false;
    }

    public static function flash($key, $value = '')
    {
        if (!empty($value)) {
            $session = self::data($key, $value);
            return true;
        } else {
            $session = self::data($key);
            self::delete($key);
            return $session;
        }
    }

    public static function delete($key = '')
    {
        $sessionKey = self::isInvalid();

        if (!empty($sessionKey)) {
            if (!empty($_SESSION[$sessionKey])) {
                if (!empty($key)) {
                    unset($_SESSION[$sessionKey][$key]);
                    return true;
                } else {
                    unset($_SESSION[$sessionKey]);
                    return true;
                }
            }
        }

        return false;
    }

    public static function isInvalid()
    {
        global $config;

        if (!empty($config)) {

            if (!empty($config['session_key'])) {
                $sessionKey = $config['session_key'];

                return $sessionKey;
            }
        } else {
            $data = ['messages' => 'Đã có lỗi xảy ra, thiếu config session vui lòng kiểm tra lại file'];
            App::$app->loadError('session', $data);
            die();
        }

        return false;
    }
}
