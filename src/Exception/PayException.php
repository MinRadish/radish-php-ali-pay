<?php
/**
 * 支付异常类
 * @authors Radish (1004622952@qq.com)
 * @date    2019-12-18 11:21 Wednesday
 */

namespace Radish\AliPay\Exception;

class PayException extends \Exception 
{
    protected $message;
    protected $result;

    public function __construct($message, $result)
    {
        $this->message = $message;
        $this->result = $result;
        $this->createLog();
    }

    public function createLog()
    {
        $path = $_SERVER['DOCUMENT_ROOT'];
        if (is_dir($path)) {
            $path .= DIRECTORY_SEPARATOR . 'AliPay';
            if (!is_dir($path)) {
                mkdir($path);
            }
            $file = $path . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
            $time = date('Y-m-d H:i:s');
            $this->message = mb_convert_encoding($this->message, 'UTF-8', mb_detect_encoding($this->message));
            file_put_contents($file, $time . PHP_EOL . $this->result() . PHP_EOL . 'message:' . $this->message . PHP_EOL, FILE_APPEND);
        }
    }

    public function message()
    {
        return $this->message;
    }

    public function result()
    {
        return mb_convert_encoding($this->result, 'UTF-8', mb_detect_encoding($this->result));
    }
}