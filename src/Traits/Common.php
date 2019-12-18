<?php
/**
 * 公用文件
 * @authors Radish (1004622952@qq.com)
 * @date    2019-12-18 14:25 Wednesday
 */

namespace Radish\AliPay\Traits;

use Radish\Network\Curl;

trait Common
{
    /**
      * 获得随机字符串
      * @param $len          需要的长度
      * @param $special      是否需要特殊符号
      * @return string       返回随机字符串
      */
    public function getRandomStr($len = 20, $special = false)
    {
        $chars = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k","l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v","w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G","H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        if($special){
            $chars = array_merge($chars, ["!", "@", "#", "$", "?", "|", "{", "/", ":", ";", "%", "^", "&", "*", "(", ")", "-", "_", "[", "]", "}", "<", ">", "~", "+", "=", ",", "."]);
        }
        $charsLen = count($chars) - 1;
        shuffle($chars);  //打乱数组顺序
        $str = '';
        for($i=0; $i<$len; $i++){
            $str .= $chars[mt_rand(0, $charsLen)]; //随机取出一位
        }

        return $str;
    }

    /**
     * 请求响应错误信息
     * @param  json $xml 响应数据
     * @param  String $fun 获取对应接口返回错误码信息
     * @return mixed    响应结果
     */
    protected function getMessage($json, $fun = '')
    {
        $array = json_decode($json, true);
        if ($array['alipay_trade_page_pay_response']['code'] != '10000') {
            $msg = '支付请求失败!';
            if ($fun && method_exists($this, $fun)) {
                $temp = $this->$fun($array['err_code']);
                $temp && $msg = $temp;
            } else {
                isset($array['alipay_trade_page_pay_response']['msg']) && $msg = $array['alipay_trade_page_pay_response']['msg'];
            }
            $this->throwx($msg, $json);
        } else {
            return $array;            
        }
    }

    /**
     * 拼接数组
     * @param  array  $params    待拼接
     * @param  string $connector 拼接符
     * @return string            拼接后字符串
     */
    public function jointString(array $params, $connector = '&')
    {
        ksort($params);
        $d = $string = '';
        foreach ($params as $key => $val) {
            $val && $string .= $d . $key . '=' . $val;
            $d = $connector;
        }

        return $string;
    }

    /**
     * 获取错误代码
     * @param  string $key 代码
     * @return String 错误代码与信息
     */
    protected function getCodeMap($key)
    {
        $codeMap = [
            //获取access_token
            '-1' => '系统繁忙，此时请开发者稍候再试',
            '40001' => 'AppSecret错误或者AppSecret不属于这个公众号，请开发者确认AppSecret的正确性',
            '40002' => '请确保grant_type字段值为client_credential',
            '40164' => '调用接口的IP地址不在白名单中，请在接口IP白名单中进行设置。（小程序及小游戏调用不要求IP地址在白名单内。）',
        ];
        $info = isset($codeMap[$key]) ? $codeMap[$key] : false;

        return $info;
    }

    /**
     * 生成订单号
     * @param  string $joint 后缀
     * @return string        返回订单号
     */
    public function orderNo($joint = null)
    {
        if (!$joint) {
            $joint = $this->getRandomStr(5);
        }
        $orderNo = date("YmdHis") . $joint;

        return $orderNo;
    }

    /**
     * 公共的请求接口的方法
     * @param  array  $params 请求参数
     * @param  string $urlKey 请求地址
     * @return mixed          响应结果
     */
    protected function sendResult(array $params, string $urlKey, bool $sslCert = false)
    {
        $option = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];
        if ($sslCert) {
            $option[CURLOPT_SSLCERTTYPE] = 'PEM';
            $option[CURLOPT_SSLCERT] = $this->rootCert;
            $option[CURLOPT_SSLKEYTYPE] = 'PEM';
            $option[CURLOPT_SSLKEY] = $this->certPublicKey;
        }
        $result = Curl::post($this->url, $params, $option);

        return $this->getMessage($result);
    }

    /**
     * 支付请求公共参数
     * @return array   参数
     */
    protected function getCommonParam()
    {
        $params = [
            'app_id' => $this->appId,
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
        ];

        return $params;
    }

    /**
     * 获取私钥资源
     * @param  string $path      秘钥路径
     * @return string            私钥资源
     */
    protected function getRsaPrivateKey($path)
    {
        $privateKey = file_get_contents($path);
        $resource = openssl_get_privatekey($privateKey);

        return $resource;
    }

    public static function throwx($msg = '位置错误', $result = 'not result')
    {
        throw new \Radish\AliPay\Exception\WeChatPayException($msg, 'not result');
    }
}