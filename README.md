# 支付宝接口调用封装

### 支付宝支付-配置项

~~~
[
    'url' => '',
    'signPattern' => '',
    'appId' => '',
    'rsaPrivateKey' => '',
    'rsaPublicKey' => '',
    'resKey' => '',
    'signType' => '',
]
~~~

- 参数说明

|:--|:--|
|url|网关|
|signPattern|秘钥方式 1:文件，2:字符串|
|appId|支付宝分配给开发者的应用ID|
|rsaPrivateKey|rsa私钥证书|
|rsaPublicKey|rsa公钥证书|
|resKey|AES密钥|
|signType|生成验签方式-默认(RSA2)|