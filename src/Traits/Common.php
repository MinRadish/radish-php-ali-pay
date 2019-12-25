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
        $key = str_replace('.', '_', $this->method) . '_response';
        if ($array[$key]['code'] != '10000') {
            $msg = '支付请求失败!';
            if ($fun && method_exists($this, $fun)) {
                $temp = $this->$fun($array['err_code']);
                $temp && $msg = $temp;
            } else {
                isset($array[$key]['msg']) && $msg = $array[$key]['msg'];
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
            'ACQ.SYSTEM_ERROR' => '接口返回错误',
            'ACQ.INVALID_PARAMETER' => '参数无效',
            'ACQ.ACCESS_FORBIDDEN' => '无权限使用接口',
            'ACQ.EXIST_FORBIDDEN_WORD' => '订单信息中包含违禁词',
            'ACQ.PARTNER_ERROR' => '应用APP_ID填写错误',
            'ACQ.TOTAL_FEE_EXCEED' => '订单总金额不在允许范围内',
            'ACQ.CONTEXT_INCONSISTENT' => '交易信息被篡改',
            'ACQ.TRADE_HAS_SUCCESS' => '交易已被支付',
            'ACQ.TRADE_HAS_CLOSE' => '交易已经关闭',
            'ACQ.BUYER_BALANCE_NOT_ENOUGH' => '买家余额不足',
            'ACQ.BUYER_BANKCARD_BALANCE_NOT_E' => '用户银行卡余额不足',
            'ACQ.ERROR_BALANCE_PAYMENT_DISABL' => '余额支付功能关闭',
            'ACQ.BUYER_SELLER_EQUAL' => '买卖家不能相同',
            'ACQ.TRADE_BUYER_NOT_MATCH' => '交易买家不匹配',
            'ACQ.BUYER_ENABLE_STATUS_FORBID' => '买家状态非法',
            'ACQ.PAYMENT_FAIL' => '支付失败',
            'ACQ.BUYER_PAYMENT_AMOUNT_DAY_LIM' => '买家付款日限额超限',
            'ACQ.BUYER_PAYMENT_AMOUNT_MONTH_L' => '买家付款月额度超限',
            'ACQ.ERROR_BUYER_CERTIFY_LEVEL_LI' => '买家未通过人行认证',
            'ACQ.PAYMENT_REQUEST_HAS_RISK' => '支付有风险',
            'ACQ.NO_PAYMENT_INSTRUMENTS_AVAIL' => '没用可用的支付工具',
            'ACQ.ILLEGAL_SIGN_VALIDTY_PERIOD' => '无效的签约有效期',
            'ACQ.MERCHANT_AGREEMENT_NOT_EXIST' => '商户协议不存在',
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
    protected function sendResult(array $params, bool $sslCert = false)
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
            'method' => $this->method,
            'charset' => 'UTF-8',
            'sign_type' => $this->signType,
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
    protected function getRsaResource($path, $keyType = 'private')
    {
        $keyContent = file_get_contents($path);
        if ($keyType == 'private') {
            $resource = openssl_get_privatekey($keyContent);
        } else {
            $resource = openssl_get_publickey($keyContent);
        }

        return $resource;
    }

    /**
     * 快捷抛出异常
     * @param  string $msg      错误信息
     * @return string $result   curl请求信息
     */
    public static function throwx($msg = '位置错误', $result = 'not result')
    {
        throw new \Radish\AliPay\Exception\PayException($msg, $result);
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    protected function characet($data, $targetCharset) 
    {
        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }

    /**
     * 回调验签
     * @param array $params   参数
     * @return bool
     */
    public function notify(array $params)
    {
        $sign = $params['sign'];
        $params['sign_type'] = null;
        $params['sign'] = null;
        $plaintext = $this->jointString($params);
        $resource = $this->getResource('public');
        if ($this->signType == 'RSA2') {
            $result = (openssl_verify($plaintext, base64_decode($sign), $resource, OPENSSL_ALGO_SHA256)===1);
        } else {
            $result = (openssl_verify($plaintext, base64_decode($sign), $resource)===1);
        }
        if ($this->signPattern == self::SIGN_PATTERN_PUBLICK_FILE) {
            openssl_free_key($resource);
        }

        return $result;
    }
}