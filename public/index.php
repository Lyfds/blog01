<?php
// 定义常量
define('ROOT', dirname(__FILE__) . '/../');

// 引入 composer 自动加载文件
require(ROOT.'vendor/autoload.php');

// 实现类的自动加载
function autoload($class)
{
    $path = str_replace('\\', '/', $class);

    require(ROOT . $path . '.php');
}
spl_autoload_register('autoload');

// 添加路由 ：解析 URL 浏览器上 blog/index  CLI中就是 blog index

if(php_sapi_name() == 'cli')
{
    $controller = ucfirst($argv[1]) . 'Controller';
    $action = $argv[2];
}
else
{
    if( isset($_SERVER['PATH_INFO']) )
    {
        $pathInfo = $_SERVER['PATH_INFO'];
        // 根据 / 转成数组
        $pathInfo = explode('/', $pathInfo);

        // 得到控制器名和方法名 ：
        $controller = ucfirst($pathInfo[1]) . 'Controller';
        $action = $pathInfo[2];
    }
    else
    {
        // 默认控制器和方法
        $controller = 'IndexController';
        $action = 'index';
    }
}


// 为控制器添加命名空间
$fullController = 'controllers\\'.$controller;


$_C = new $fullController;
$_C->$action();

// 加载视图
// 参数一、加载的视图的文件名
// 参数二、向视图中传的数据
function view($viewFileName, $data = [])
{
    // 解压数组成变量
    extract($data);

    $path = str_replace('.', '/', $viewFileName) . '.html';

    // 加载视图
    require(ROOT . 'views/' . $path);
}

// 获取当前 URL 上所有的参数，并且还能排除掉某些参数
// 参数：要排除的变量
function getUrlParams($except = [])
{
    // ['odby','odway']
    // 循环删除变量
    foreach($except as $v)
    {
        unset($_GET[$v]);

        // unset($_GET['odby']);
        // unset($_GET['odway']);
    }

    /*
    $_GET['keyword'] = 'xzb';
    $_GET['is_show] = 1

    // 拼出：  keyword=abc&is_show=1
    */

    $str = '';
    foreach($_GET as $k => $v)
    {
        $str .= "$k=$v&";
    }

    return $str;

}

// 获取配置文件（特点：无论调用多次，只包含一次配置文件）
// 静态局部变量：函数执行结束，也不会销毁，一直存在到整个脚本结束
// 普通局部亦是：函数执行完就销毁了
function config($name)
{
    static $config = null;
    if($config === null)
    {
        // 引入配置文件 
        $config = require(ROOT.'config.php');
    }
    return $config[$name];
}