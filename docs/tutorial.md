## 云函数引入 php-sdk 快速教程

### 准备工作

- 安装 CLI 工具 CLI 工具[文档参考](https://github.com/TencentCloudBase/cloudbase-framework/blob/master/CLI_GUIDE.md)

```bash
npm i -g @cloudbase/cli
```

- 登录云开发

```bash
cloudbase login
```

- 进入当前 php 项目根目录下进行初始化

```bash
cloudbase init --without-template
```

- 在 php 项目根目录下创建 functions 文件夹存放云函数，进入 functions 文件夹并新建 phpTest 云函数

![](https://main.qcloudimg.com/raw/fb7d40e8af0842a5abc63e6f29d33c42.png)

- phpTest 云函数中新建并编辑入口文件 index.php 如下

```php
<?php

function main_handler($event, $context)
{
  require_once __DIR__ . DIRECTORY_SEPARATOR . 'tcb-php-sdk' . DIRECTORY_SEPARATOR . 'autoload.php';
  $tcb = new TencentCloudBase\TCB(array("secretId" => "xxx", "secretKey" => "xxx"));

  print($tcb)
  // your code

 return 'helllo world'
}
```

- phpTest 云函数中编辑 composer.json 如下

```json
{
  "require": {
    "tencentcloudbase/tcb-php-sdk": "1.0"
  }
}
```

- 配置

`cloudbase init` 之后会创建云开发的配置文件 `cloudbaserc.json`，可在配置文件的 plugins 里修改和写入插件配置

```json
{
  "envId": "xxx", // 替换为开发者自己的envId
  "framework": {
    "plugins": {
      "function": {
        "use": "@cloudbase/framework-plugin-function",
        "inputs": {
          "functionRootPath": "./functions",
          "functions": [
            {
              "name": "phpTest",
              "timeout": 60,
              "envVariables": {},
              "runtime": "Php7",
              "memorySize": 128,
              "handler": "index.main_handler"
            }
          ]
        }
      }
    }
  }
}
```

### 云函数部署

php 项目根目录下一键部署

```bash
cloudbase framework:deploy
```

### 云函数调用

- 进入小程序开发者工具，同步云函数列表，可以看到列表中有上传的云函数 phpTest，编写云函数调用代码

示例代码

```javascript
wx.cloud
  .callFunction({
    name: 'phpTest',
    data: {},
  })
  .then((res) => {
    console.log(res)
  })
  .catch((err) => {
    console.error(err)
  })
```
