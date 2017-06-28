# 安装

```
composer require stone/open_api_sdk:dev-master
```

# 使用

加密解密：

```php
use Stone\SDK\Util\Crypt;

$key = config('pub_key');
$params = [
    'appId' => '123123123',
    'outTradeNo' => '456456456456234',
];
$encrypted = Crypt::partnerEncrypt($params, $key);
//var_dump($encrypted);

$private_key = config('private_key');
$data = Crypt::platformDecrypt($encrypted, $private_key);
//var_dump($data);

```

# 开发测试

下载代码

```
git clone https://github.com/StoneGroup/open_api_php_sdk.git
```
初始化测试环境

```
cd open_api_php_sdk
composer dumpautoload
```

单元测试

```
phpunit
```


