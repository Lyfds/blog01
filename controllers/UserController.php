<?php
namespace controllers;

// 引入模型类
use models\User;

class UserController
{
    public function register()
    {
        // 显示视图
        view('users.add');
    }

    public function hello()
    {
        // 取数据
        $user = new User;
        $name = $user->getName();

        // 加载视图
        view('users.hello', [
            'name' => $name
        ]);
    }

    public function world()
    {
        echo 'world';
    }

    public function store()
    {
        // 1. 接收表单
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        // 2. 插入到数据库中
        $user = new User;
        $ret = $user->add($email, $password);
        if(!$ret)
        {
            die('注册失败！');
        }

        // 3. 把消息放到队列中

        // 从邮箱地址中取出姓名 
        $name = explode('@', $email);
        // 构造收件人地址[    fortheday@126.com   ,    fortheday  ]
        $from = [$email, $name[0]];

        // 构造消息数组
        $message = [
            'title' => '欢迎加入全栈1班',
            'content' => "点击以下链接进行激活：<br> <a href=''>点击激活</a>。",
            'from' => $from,
        ];
        // 把消息转成字符串(JSON ==> 序列化)
        $message = json_encode($message);

        // 放到队列中
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 32768,
        ]);

        $redis->lpush('email', $message);

        echo 'ok';

    }
}