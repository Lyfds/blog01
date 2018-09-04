<?php
namespace libs;

// 三私一公

class Redis
{
    private static $redis = null;
    private function __clone(){}
    private function __construct(){}

    // 获取 redis 对象 
    public static function getInstance()
    {
        // 如果还没有 redis 就生成一个
        // 只有每 一次 才会连接
        if(self::$redis === null)
        {
            // 放到队列中
            self::$redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 32768,
            ]);
        }
        // 直接返已有的redis 对象
        return self::$redis;
    }
}