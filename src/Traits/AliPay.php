<?php
/**
 * 支付操作
 * @authors Radish (1004622952@qq.com)
 * @date    2019-12-18 14:28 Wednesday
 */

namespace Radish\AliPay\Traits;

trait AliPay
{
    /**
     * 同一下单
     * @param  array $options 请求参数
     * @return           
     */
    public function orderUnify(array $options)
    {
        $params = $this->formatParam($options);
        $params['sign'] = base64_encode($this->sgin($params));
        if ($this->isCurl) {
            return $this->sendResult($params);
        } else {
            return $params;
        }
    }

    /**
     * 同一下单
     * @param  array $options 请求参数
     * @return           
     */
    public function tradeRefund(array $content, $method = 'alipay.trade.refund')
    {
        $this->method = $method;
        $params = [
            'biz_content' => $content,
        ];
        $result = $this->orderUnify($params);

        return $result;
    }

    /**
     * 格式化请求初始参数
     * @param  array $options 初始值
     * @return array          格式化后的值
     */
    private function formatParam(array $options)
    {
        $params = array_merge($this->getCommonParam(), $options);
        switch ($this->signPattern) {
            case self::SIGN_PATTERN_PUBLICK_FILE:
                # code...
                break;
            
            case self::SIGN_PATTERN_PUBLICK:
                break;
            
            default:
                self::throwx('无效的秘钥方式');
                break;
        }
        $params['biz_content'] = json_encode($params['biz_content']);

        return $params;
    }

    /**
     * 生成签名验签
     * @param  array $options 格式化后参数
     * @return string         验签后字符
     */
    private function sgin(array $params)
    {
        $plaintext = $this->jointString($params);
        $resource = $this->getResource();
        if ($this->signType == 'RSA2') {
            openssl_sign($plaintext, $sign, $resource, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($plaintext, $sign, $resource);
        }
        if ($this->signPattern == self::SIGN_PATTERN_PUBLICK_FILE) {
            openssl_free_key($resource);
        }

        return $sign;
    }

    /**
     * 获取证书资源
     * @param  array $type  秘钥类型
     * @return string       证书资源
     */
    private function getResource($type = 'rsaPrivateKey')
    {
        switch ($type) {
            case 'certPublicKey':
                $key = $this->certPublicKey;
                $keyIdentifying = "PUBLIC";
                break;
            
            case 'rsaPrivateKey':
                $key = $this->rsaPrivateKey;
                $keyIdentifying = "RSA PRIVATE";
                break;
            
            case 'rsaPublicKey':
                $key = $this->rsaPublicKey;
                $keyIdentifying = "PUBLIC";
                break;
        }
        switch ($this->signPattern) {
            case self::SIGN_PATTERN_PUBLICK_FILE:
                $resource = $this->getRsaResource($key);
                break;
            
            case self::SIGN_PATTERN_PUBLICK:
                if (strstr($key, '-----')) {
                    $resource = $key;
                } else {
                    $resource = "-----BEGIN {$keyIdentifying} KEY-----\n" . wordwrap($key, 64, "\n", true) . "\n-----END {$keyIdentifying} KEY-----";
                }
                break;
            
            default:
                self::throwx('无效的秘钥方式');
                break;
        }

        return $resource;
    }
}