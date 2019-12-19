<?php
/**
 * 阿里支付
 * @authors Radish (1004622952@qq.com)
 * @date    2019-12-18 10:59 Wednesday
 */

namespace Radish\AliPay;

abstract class AliPay 
{
    use Traits\Common;
    use Traits\AliPay;

    const SIGN_PATTERN_PUBLICK_FILE = 1;
    const SIGN_PATTERN_PUBLICK = 2;

    /**
     * 支付宝接口请求地址-正式环境
     * @var string
     */
    protected $url = 'https://openapi.alipay.com/gateway.do';

    /**
     * 支付宝接口私钥公钥方式
     * @var string
     */
    protected $signPattern = self::SIGN_PATTERN_PUBLICK_FILE;

    /**
     * 支付宝分配给开发者的应用ID
     * @var string
     */
    protected $appId;

    /**
     * 支付宝公钥证书
     * @var string
     */
    protected $certPublicKey;

    /**
     * 支付宝根证书
     * @var string
     */
    protected $rootCert;

    /**
     * 应用公钥证书
     * @var string
     */
    protected $appCertPublicKey;

    /**
     * rsa私钥证书
     * @var string
     */
    protected $rsaPrivateKey;

    /**
     * rsa公钥证书
     * @var string
     */
    protected $rsaPublicKey;

    /**
     * AES密钥
     * @var string
     */
    protected $resKey;

    /**
     * 生成验签方式
     * @var string
     */
    protected $signType = 'RSA2';

    /**
     * 调用接口
     * @var string
     */
    public $method = 'alipay.trade.page.pay';

   /**
     * 是否curl提交参数
     * @var string
     */
    public $isCurl = true;

    public function getSignPatternField($key = null)
    {
        $map = [
            self::SIGN_PATTERN_PUBLICK_FILE => '公钥文件方式',
            self::SIGN_PATTERN_PUBLICK => '公钥方式',
        ];
        isset($map[$key]) && $map = $map[$key];

        return $map;        
    }

    public function __construct()
    {
        
    }
    
}