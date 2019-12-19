# 统一下单

## 下单接口

### alipay.trade.page.pay

- 应用场景-PC支付

**示例**

~~~
$aliPay = new AliPay;
$aliPay->method = 'alipay.trade.page.pay';
$aliPay->isCurl = false;
$params = [
    'biz_content' => [
        'out_trade_no' => '20191219095911',
        'product_code' => 'FAST_INSTANT_TRADE_PAY',
        'total_amount' => '0.01',
        'subject' => 'test',
    ],
];
$fromParams = $aliPay->orderUnify($params);
~~~
