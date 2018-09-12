<?php
namespace controllers;
use Endroid\QrCode\QrCode;
class QrcodeController {
    public function qrcode() {
        $str = $_GET['code'];
        $qrCode = new QrCode($str);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }
}