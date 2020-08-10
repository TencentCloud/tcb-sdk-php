# Tencent Cloud Base(TCB) php SDK

## 目录

- [介绍](#介绍)
- [安装](#安装)
- [开发指引](#开发指引)
- [文档](#文档)

## 介绍

TCB 提供开发应用所需服务和基础设施。tcb-php-sdk 让你可以在服务端（如腾讯云云函数或 CVM 等）使用 php 服务访问 TCB 的的服务。

需要 php7 及以上版本。

## 安装

composer require tcb-php-sdk

> 国内使用时，可切换为国内镜像: composer config -g repo.packagist composer https://packagist.phpcomposer.com

## 开发指引

[如何使用 php-sdk 开发 php 云函数](docs/tutorial.md)

## 文档

- [初始化](docs/initialization.md)
- [存储](docs/storage.md)
- [数据库](docs/database.md)
- [云函数](docs/functions.md)
