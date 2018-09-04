<?php
/* 
所有其它模型的父模型
*/

namespace models;

use PDO;

class Base
{
    // 保存 PDO 对象
    public static $pdo = null;

    public function __construct()
    {
        if(self::$pdo === null)
        {
            // 取日志的数据
            self::$pdo = new PDO('mysql:host=127.0.0.1;dbname=blog', 'root', '123456');
            self::$pdo->exec('SET NAMES utf8');
        }
    }
}
