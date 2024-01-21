<?php

class Connection
{
    private static $instance = null, $conn;

    private function __construct($config)
    {
        //Kết nối database
        try {

            //Cấu hình dsn
            $dsn = 'mysql:dbname=' . $config['db'] . ';host=' . $config['host'];

            //Cấu hình $options
            /*
             * - Cấu hình utf8
             * - Cấu hình ngoại lệ khi truy vấn bị lỗi
             * */
            $options = [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];
            //Câu lệnh kết nối
            $con = new PDO($dsn, $config['user'], $config['pass'], $options);

            self::$conn = $con;
        } catch (Exception $exception) {
            $mess = $exception->getMessage();
            App::$app->loadError('database', ['message' => $mess]);
            die();
        }
    }


    public static function getInstance($config)
    {
        if (self::$instance == null) {
            $className = __CLASS__;
            new $className($config);
            self::$instance = self::$conn;
        }

        return self::$instance;
    }
}
