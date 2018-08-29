<?php
// 定义常量
define('ROOT', dirname(__FILE__) . '/../');

// 实现类的自动加载
function autoload($class)
{
    $path = str_replace('\\', '/', $class);

    require(ROOT . $path . '.php');
}
spl_autoload_register('autoload');



$userController = new controllers\UserController;
$userController->hello();


// 加载视图
// 参数一、加载的视图的文件名
// 参数二、向视图中传的数据
function view($viewFileName, $data = [])
{
    extract($data);

    $path = str_replace('.', '/', $viewFileName) . '.html';

    // 加载视图
    require(ROOT . 'views/' . $path);
}
