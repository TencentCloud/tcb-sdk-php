# Tencent Cloud Base(TCB) php SDK


## 目录
* [介绍](#介绍)
* [安装](#安装)
* [文档](#文档)


## 介绍
TCB提供开发应用所需服务和基础设施。tcb-php-sdk 让你可以在服务端（如腾讯云云函数或CVM等）使用php服务访问TCB的的服务。

需要php7及以上版本。

## 安装
require_once 'tcb-php-sdk/autoload.php'; //使用方式：通过源码包引入 ，相对路径自行调整 
php-sdk beta版使用方式
### 1. 准备工作
1.1 安装cli工具 npm i -g @cloudbase/cli@0.0.7-8
1.2 php-sdk 源码包  地址https://github.com/TencentCloudBase/tcb-admin-php
### 2. 云函数代码编写
示例代码zip包下载https://share.weiyun.com/58dQW4M ，基于代码修改即可（代码包中已引入php-sdk文件）
[1]index.php内容
```javascript
<?php

function main_handler($event, $context)
{  
  require_once __DIR__ . DIRECTORY_SEPARATOR . 'tcb-admin-php' . DIRECTORY_SEPARATOR . 'autoload.php';  
  $tcb = new TencentCloudBase\TCB(array("secretId" => "xxx", "secretKey" => "xxx"));  

  print($tcb)  
  // your code   

  return 'helllo world'}
```
[2]tcb.json内容
```javascript
{  
"deploys": [    
    {     
    "name": "phpTestFunc", // 指定当前云函数的function name     
    "path": "./",     
    "type": "function",     
   "envId": "xxx", // tcb envid      
   "override": true // 是否覆盖同名函数   
    }  
  ]
}
```
### 3. 云函数部署
3.1 上传云函数：在当前云函数文件夹根目录下输入 tcb deploy --runtime Php7（未登录需要先tcb login（请正确输入secretID,secretKey），cli工具文档参考<https://github.com/TencentCloudBase/cloud-base-cli)>
部署成功显示Depoly serverless function xxx success!
### 4. 云函数调用
4.1 进入小程序开发者工具，同步云函数列表，可以看到列表中有上传的云函数phpTestFunc，编写云函数调用代码
```javascript
wx.cloud.callFunction({      
      name: 'phpTestFunc',      
      data: {      
      }   
    }).then(res => {      
      console.log(res)   
    }).catch(err => {      
      console.error(err);    
    });
```
### 5. php sdk 使用参考内置readme文档

## 文档
* [初始化](docs/initialization.md)
* [存储](docs/storage.md)
* [数据库](docs/database.md)
* [云函数](docs/functions.md)
* [如何结合sdk编写php云函数](docs/tutorial.md)
