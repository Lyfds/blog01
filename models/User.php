<?php
namespace models;
use PDO;
class User extends Base
{
    //获取金额
    public function getMoney() {
        $id = $_SESSION['id'];
        $stmt = self::$pdo->prepare('SELECT money FROM users WHERE id=?');
        $stmt->execute([$id]);
        $money = $stmt->fetch(PDO::FETCH_COLUMN);
        //更新到SESSION中
        $_SESSION['money'] = $money;
        return $money;
        
    }
   // 为用户增加金额
public function addMoney($money, $userId)
{
    $stmt = self::$pdo->prepare("UPDATE users SET money=money+? WHERE id=?");
    return $stmt->execute([
        $money,
        $userId
    ]);
}
    public function add($email,$password)
    {
        $stmt = self::$pdo->prepare("INSERT INTO users (email,password) VALUES(?,?)");
        return $stmt->execute([
                                $email,
                                $password,
                            ]);
    }

    public function login($email, $password)
    {
        // 根据 email 和 password 查询数据库
        $stmt = self::$pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
        // 执行 SQL
        $stmt->execute([
            $email,
            $password
        ]);
        // 取出数据
        $user = $stmt->fetch();
        // 是否有这个账号
        if( $user )
        {
            // 登录成功，把用户信息保存到 SESSION
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['money'] = $user['money'];
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}