# 京东云交易集成包

[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/jingdong-cloud-trade-bundle)](https://packagist.org/packages/tourze/jingdong-cloud-trade-bundle)
[![License](https://img.shields.io/packagist/l/tourze/jingdong-cloud-trade-bundle)](https://packagist.org/packages/tourze/jingdong-cloud-trade-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/monorepo/test.yml?branch=master)](https://github.com/tourze/monorepo)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/monorepo)](https://codecov.io/gh/tourze/monorepo)

[English](README.md) | [中文](README.zh-CN.md)

## Table of Contents

- [Features](#features)
- [Dependencies](#dependencies)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Advanced Usage](#advanced-usage)
- [技术架构](#技术架构)
- [实体说明](#实体说明)
- [枚举类](#枚举类)
- [Repository使用说明](#repository使用说明)
- [事件处理](#事件处理)
- [服务层](#服务层)
- [命令行工具](#命令行工具)
- [示例代码](#示例代码)
- [License](#license)

## Features

- 完整的京东开放平台API集成
- 标准化的Symfony Bundle架构
- 支持订单、商品、物流、售后等全业务流程
- 基于PHP 8.1+枚举的类型安全设计
- 完整的实体关系映射
- 丰富的Repository查询方法
- 事件驱动的消息处理机制

## Dependencies

本Bundle基于以下核心依赖：

- **PHP**: ^8.1
- **Symfony**: ^6.4
- **Doctrine ORM**: ^3.0
- **tourze/enum-extra**: 枚举扩展支持
- **tourze/arrayable**: 数组化支持
- **tourze/doctrine-***: Doctrine相关Bundle集合

## Installation

```bash
composer require tourze/jingdong-cloud-trade-bundle
```

## Quick Start

### 1. 注册Bundle

在 `config/bundles.php` 中注册：

```php
return [
    // 其他bundles...
    JingdongCloudTradeBundle\JingdongCloudTradeBundle::class => ['all' => true],
];
```

### 2. 配置数据库

运行数据库迁移：

```bash
php bin/console doctrine:migrations:migrate
```

### 3. 基本使用

```php
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Repository\OrderRepository;

// 获取京东账户
$accountRepository = $container->get(AccountRepository::class);
$account = $accountRepository->findOneBy(['appKey' => 'YOUR_APP_KEY']);

// 查询订单
$orderRepository = $container->get(OrderRepository::class);
$order = $orderRepository->findByOrderIdAndAccount('JD123456789', $account);
```

## Advanced Usage

### OAuth认证流程

```php
// 1. 获取授权URL
$authService = $container->get(AuthService::class);
$authUrl = $authService->getAuthorizationUrl($account, $callbackUrl);

// 2. 处理回调并获取访问令牌
$accessToken = $authService->getAccessToken($account, $authCode);
```

### API客户端使用

```php
use JingdongCloudTradeBundle\Service\Client;

$client = $container->get(Client::class);
$response = $client->execute($account, 'jingdong.ctp.order.query.getOrderDetail', [
    'orderId' => 'JD123456789'
]);
```

## 技术架构

本Bundle采用Symfony标准架构设计，主要包含以下部分：

- **实体层（Entity）**：数据模型定义
- **仓储层（Repository）**：数据访问逻辑
- **枚举层（Enum）**：状态和类型定义
- **服务层（Service）**：业务逻辑处理
- **事件处理（EventListener）**：消息订阅处理

京东POP = 京东 Platform Open Plan

## 实体说明

### 账户关联

所有与京东API交互的实体都必须关联到`Account`实体，以确保：
- 获取正确的授权凭证
- 区分不同商户的数据
- 追踪API调用记录

关联方式示例：
```php
#[ORM\ManyToOne(targetEntity: Account::class)]
#[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false)]
private Account $account;
```

### 订单相关实体

- **Order** - 订单主体，记录订单基本信息
  - 对应API: [订单查询API](https://open.jd.com/home/home#/doc/api?
    apiCateId=881&apiId=16491&apiName=jingdong.ctp.order.query.getOrderDetail)
  - 与`Account`实体关联，表示订单所属的京东账户
- **OrderItem** - 订单商品项，记录订单中的商品信息
  - 对应API: [订单商品信息API](https://open.jd.com/home/home#/doc/api?
    apiCateId=881&apiId=16491&apiName=jingdong.ctp.order.query.getOrderDetail)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`
- **Payment** - 支付信息，记录订单支付相关数据
  - 对应API: [支付结果查询](https://open.jd.com/home/home#/doc/api?
    apiCateId=881&apiId=16506&apiName=jingdong.ctp.pay.queryPayResult)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`
- **Invoice** - 发票信息，记录订单发票数据
  - 对应API: [发票信息查询](https://open.jd.com/home/home#/doc/api?
    apiCateId=881&apiId=16430&apiName=jingdong.ctp.invoice.getInvoiceDetail)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`

### 商品相关实体

- **Sku** - 商品信息，记录京东商品数据
  - 对应API: [商品详情查询](https://open.jd.com/home/home#/doc/api?
    apiCateId=881&apiId=16478&apiName=jingdong.ctp.sku.getSkuDetail)
  - 与`Account`实体关联，表示SKU所属的京东账户
- **Category** - 商品分类信息
  - 对应API: [商品分类查询](https://open.jd.com/home/home#/doc/api?
    apiCateId=881&apiId=16443&apiName=jingdong.ctp.sku.getCategoryDetail)
  - 与`Account`实体关联，表示分类所属的京东账户
- **Brand** - 品牌信息
  - 对应API: [品牌查询](https://open.jd.com/home/home#/doc/api?
    apiCateId=881&apiId=16442&apiName=jingdong.ctp.sku.getBrandsByCid)
  - 与`Account`实体关联，表示品牌所属的京东账户

### 物流相关实体

- **Logistics** - 物流信息，记录订单物流信息
  - 对应API: [物流信息查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16420&apiName=jingdong.ctp.logistics.getTrackInfo)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`
- **DeliveryAddress** - 收货地址，记录用户收货地址
  - 对应API: [地址管理](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16479&apiName=jingdong.ctp.order.getChildAreaList)
  - 与`Account`实体关联，表示地址所属的京东账户
- **Warehouse** - 仓库信息，记录京东仓库信息
  - 对应API: [仓库信息查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16427&apiName=jingdong.ctp.wareHouse.getWareHousesByOrder)
  - 与`Account`实体关联，表示仓库所属的京东账户

### 售后相关实体

- **AfsService** - 售后服务单，记录售后服务申请信息
  - 对应API: [售后服务单查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16510&apiName=jingdong.ctp.afs.servicebill.getAfsServiceDetail)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`
- **Comment** - 订单评论，记录用户对订单的评价
  - 对应API: [评价查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16439&apiName=jingdong.ctp.comment.querySkuComments)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`

## 枚举类

所有枚举类遵循PHP 8.1后原生枚举实现，并实现以下接口：
- `Tourze\EnumExtra\Labelable` - 标签接口
- `Tourze\EnumExtra\Itemable` - 项目接口
- `Tourze\EnumExtra\Selectable` - 选择器接口

### 订单相关枚举

- **OrderStateEnum** - 订单状态
  - 文档: [订单状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16491&apiName=jingdong.ctp.order.query.getOrderDetail)
- **PayTypeEnum** - 支付类型
  - 文档: [支付类型说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16506&apiName=jingdong.ctp.pay.queryPayResult)
- **ItemStateEnum** - 订单商品状态
  - 文档: [订单商品状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16491&apiName=jingdong.ctp.order.query.getOrderDetail)

### 商品相关枚举

- **SkuStateEnum** - 商品状态
  - 文档: [商品状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16478&apiName=jingdong.ctp.sku.getSkuDetail)
- **CategoryStateEnum** - 分类状态
  - 文档: [分类状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16443&apiName=jingdong.ctp.sku.getCategoryDetail)

### 发票相关枚举

- **InvoiceTypeEnum** - 发票类型
  - 文档: [发票类型说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16430&apiName=jingdong.ctp.invoice.getInvoiceDetail)

### 售后相关枚举

- **AfsTypeEnum** - 售后类型
  - 文档: [售后类型说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16510&apiName=jingdong.ctp.afs.servicebill.getAfsServiceDetail)
- **AfsStateEnum** - 售后状态
  - 文档: [售后状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16510&apiName=jingdong.ctp.afs.servicebill.getAfsServiceDetail)

## Repository使用说明

### 通用原则

所有Repository都遵循以下原则：
- 通过Account实体筛选数据，确保数据隔离
- 提供基于京东API ID的查询方法
- 支持分页和排序
- 包含常用的业务查询方法

### AccountRepository

```php
// 查找有效账户
$account = $accountRepository->findOneValid();

// 根据AppKey查找账户
$account = $accountRepository->findOneBy(['appKey' => 'YOUR_APP_KEY']);
```

### OrderRepository

```php
// 查询指定账户下的订单
$order = $orderRepository->findByOrderIdAndAccount('JD123456789', $account);

// 查询指定时间范围内的订单
$orders = $orderRepository->findByDateRangeAndAccount(
    $account,
    new \DateTime('2023-01-01'),
    new \DateTime('2023-12-31')
);

// 查询指定状态的订单
$orders = $orderRepository->findByStateAndAccount(
    OrderStateEnum::WAIT_SELLER_STOCK_OUT,
    $account
);
```

### SkuRepository

```php
// 查询指定账户下的商品
$sku = $skuRepository->findBySkuIdAndAccount('123456', $account);

// 查询促销商品
$promotionSkus = $skuRepository->findPromotionSkusByAccount(
    $account,
    ['price' => 'ASC'],
    10,
    0
);
```

### AfsServiceRepository

```php
// 查询售后服务单
$afsService = $afsServiceRepository->findByAfsIdAndAccount(
    'AFS123456789',
    $account
);
```

## 事件处理

Bundle支持以下事件订阅：

### OrderSubscriber

监听订单相关事件：
- 订单创建事件
- 订单状态变更事件
- 支付完成事件

```php
use JingdongCloudTradeBundle\Event\OrderCreatedEvent;

// 在事件订阅器中处理订单创建
public function onOrderCreated(OrderCreatedEvent $event): void
{
    $order = $event->getOrder();
    // 处理订单创建逻辑
}
```

## 服务层

### AuthService

负责OAuth认证流程：

```php
// 获取授权URL
$authUrl = $authService->getAuthorizationUrl($account, $callbackUrl);

// 处理授权回调
$accessToken = $authService->getAccessToken($account, $authCode);

// 刷新访问令牌
$newToken = $authService->refreshAccessToken($account);
```

### Client

API客户端服务：

```php
// 执行API调用
$response = $client->execute($account, $method, $params);

// 批量API调用
$responses = $client->batchExecute($account, $requests);
```

## 命令行工具

### 商品同步命令

```bash
# 同步所有账户的商品信息
php bin/console jingdong:sku:sync

# 同步指定账户的商品信息
php bin/console jingdong:sku:sync --account-id=123

# 同步指定分类的商品信息
php bin/console jingdong:sku:sync --category-id=456

# 强制重新同步
php bin/console jingdong:sku:sync --force
```

### 地区信息同步命令

```bash
# 同步地区信息
php bin/console jingdong-pop:sync-area:list
```

## 示例代码

### 订单查询示例

```php
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Repository\OrderRepository;
use JingdongCloudTradeBundle\Enum\OrderStateEnum;

// 获取京东账户
$accountRepository = $container->get(AccountRepository::class);
$account = $accountRepository->findOneBy(['appKey' => 'YOUR_APP_KEY']);

// 查询订单
$orderRepository = $container->get(OrderRepository::class);

// 查询单个订单
$order = $orderRepository->findByOrderIdAndAccount('JD123456789', $account);

// 查询待发货订单
$pendingOrders = $orderRepository->findByStateAndAccount(
    OrderStateEnum::WAIT_SELLER_STOCK_OUT,
    $account,
    ['orderTime' => 'DESC'],
    20,
    0
);

// 查询指定时间范围的订单
$orders = $orderRepository->findByDateRangeAndAccount(
    $account,
    new \DateTime('2023-01-01'),
    new \DateTime('2023-12-31')
);
```

### 商品管理示例

```php
use JingdongCloudTradeBundle\Repository\SkuRepository;
use JingdongCloudTradeBundle\Enum\SkuStateEnum;

// 查询指定账户下的商品
$skuRepository = $container->get(SkuRepository::class);
$sku = $skuRepository->findBySkuIdAndAccount('123456', $account);

// 查询促销商品
$promotionSkus = $skuRepository->findPromotionSkusByAccount(
    $account,
    ['price' => 'ASC'],
    10,
    0
);
```

### 售后服务申请示例

```php
// 售后服务申请示例
use JingdongCloudTradeBundle\Entity\AfsService;
use JingdongCloudTradeBundle\Enum\AfsTypeEnum;
use JingdongCloudTradeBundle\Repository\AfsServiceRepository;
use JingdongCloudTradeBundle\Repository\OrderRepository;

// 获取订单信息
$orderRepository = $container->get(OrderRepository::class);
$order = $orderRepository->findByOrderId('JD123456789');

// 创建售后申请并关联订单
$afsService = new AfsService();
$afsService->setOrder($order)  // 通过订单关联到Account
    ->setOrderItemId('87654321')
    ->setAfsType(AfsTypeEnum::REFUND_GOODS)
    ->setApplyReason('商品质量问题')
    ->setDescription('商品存在明显质量缺陷');

$entityManager->persist($afsService);
$entityManager->flush();

// 查询售后服务单
$afsServiceRepository = $container->get(AfsServiceRepository::class);
$afsService = $afsServiceRepository->findByAfsIdAndAccount(
    'AFS123456789',
    $order->getAccount()
);
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.