# 支付宝接口调用封装

*需自定义一个类并继承 Radish\AliPay\AliPay 自定义配置参数*

- PC网站支付,WAP网站支付,扫码支付,退款 

### 支付宝支付-配置项

~~~
[
    'url' => '',
    'signPattern' => '',
    'appId' => '',
    'rsaPrivateKey' => '',
    'rsaPublicKey' => '',
    'certPublicKey' => '',
    'resKey' => '',
    'signType' => '',
]
~~~

- 参数说明

|字段|说明|
|:--|:--|
|url|网关|
|signPattern|秘钥方式 1:文件，2:字符串|
|appId|支付宝分配给开发者的应用ID|
|rsaPrivateKey|rsa私钥证书(应用私钥)|
|rsaPublicKey|rsa公钥证书(应用公钥)|
|certPublicKey|支付宝公钥|
|resKey|AES密钥|
|signType|生成验签方式-默认(RSA2)|