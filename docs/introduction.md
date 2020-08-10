## 介绍

TCB 提供开发应用所需服务和基础设施。tcb-php-sdk 让你可以在服务端（如腾讯云云函数或 CVM 等）使用 php 服务访问 TCB 的的服务。

需要 php7 及以上版本。

## 使用方式：composer install

composer require tcb-php-sdk

> 国内使用时，可切换为国内镜像: composer config -g repo.packagist composer https://packagist.phpcomposer.com

```php
require 'vendor/autoload.php';

use TencentCloudBase\TCB as TCB;
$tcb = new TCB([]);
```
