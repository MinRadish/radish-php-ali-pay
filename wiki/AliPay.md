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

### alipay.trade.wap.pay

- 应用场景-WAP网站支付

**示例**

~~~
$aliPay = new AliPay;
$aliPay->method = 'alipay.trade.wap.pay';
$aliPay->isCurl = false;
$params = [
    'notify_url' => url('index/index/notify', [], false, true),
    'biz_content' => [
        'out_trade_no' => date('YmdHis'),
        'total_amount' => '0.01',
        'subject' => '阿斯达四大',
        'quit_url' => url('/', [], false, true),
        'product_code' => 'QUICK_WAP_WAY'
    ],
];
$params = $aliPay->orderUnify($params);
$url = $aliPay->getUrl();
~~~

### alipay.trade.refund

- 应用场景-统一退款

**示例**

~~~
$aliPay = new AliPay;
$params = [
    'out_trade_no' => '20191231155819',
    'refund_amount' => '0.01',
];
try {
    $result = $aliPay->tradeRefund($params);
    return success('退款成功！');
} catch (\Exception $e) {
    return error($e->getMessage());
}
~~~