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
     * RSA私钥证书
     * @var string
     */
    protected $rsaPrivateKey;

    /**
     * RSA公钥证书
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

    /**
     * 文件编码
     * @var string
     */
    protected $fileCharset = 'UTF-8';

    /**
     * 表单提交字符集编码
     * @var string
     */
    protected $postCharset = 'UTF-8';

    public function getSignPatternField($key = null)
    {
        $map = [
            self::SIGN_PATTERN_PUBLICK_FILE => '公钥文件方式',
            self::SIGN_PATTERN_PUBLICK => '公钥方式',
        ];
        isset($map[$key]) && $map = $map[$key];

        return $map;        
    }

    /**
     * 获取URL地址
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 构造方法
     */
    public function __construct(array $params = [])
    {
        foreach ($params as $key => $val) {
            if (property_exists($this, $key) && !empty($val)) {
                $this->$key = $val;
            }
        }
    }
    
}