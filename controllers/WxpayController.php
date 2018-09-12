<?php
namespace controllers;
use Yansongda\Pay\Pay;
class WxpayController {
    protected $config = [
        'app_id' => 'wx426b3015555a46be', // 公众号 APPID
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',
        'notify_url' => 'http://requestbin.fullcontact.com/r6s2a1r6',
    ];
    public function pay() {
        $sn = $_POST['sn'];
        $order = new \models\Order;
        $data = $order->findBySn($sn);
        if($data['status'] == 0) {
            $ret = Pay::wechat($this->config)->scan([
                'out_trade_no' => $data['sn'],
                'total_fee' => $data['money'] * 100,
                'body' => '智聊系统用户充值：'.$data['money'].'元',
            ]);
            if($ret->return_code == 'SUCCESS' && $ret->result_code == 'SUCCESS') {
                view('users.wxpay',[
                    'code' => $ret->code_url,
                    'sn' => $sn
                ]);
            }
        }else{
            die('订单状态不允许支付~');
        }
    }
    public function notify() {
        $pay = Pay::wechat($this->config);
        try {
            $data = $pay->verify();
            if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS') {
                $order = new \models\Order;
                $orderInfo = $order->findBySn($data->out_trade_no);
                if($orderInfo['status'] == 0) {
                    $order->startTrans();
                    $ret1 = $order->setPaid($data->out_trade_no);
                    $user = new \models\User;
                    $ret2 = $user->addMoney($orderInfo['money'],$orderInfo['user_id']);
                    if($ret1 && $ret2) {
                        $order->commit();
                    }
                    else{
                        $order->rollback();
                    }
                }
            } 
        }catch(Exception $e) {
             var_dump($e->getMessage() );
        }
        $pay->success()->send();
    }
}