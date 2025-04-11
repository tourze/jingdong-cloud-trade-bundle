# 京东云交易集成包

## 模块说明

本Bundle实现了京东云交易API的集成，提供完整的订单管理、商品管理、物流管理和售后服务等功能。

京东POP = 京东 Platform Open Plan

## 技术架构

本Bundle采用Symfony标准架构设计，主要包含以下部分：
- 实体层（Entity）：数据模型定义
- 仓储层（Repository）：数据访问逻辑
- 枚举层（Enum）：状态和类型定义
- 服务层（Service）：业务逻辑处理
- 事件处理（EventListener）：消息订阅处理

## 实体说明

### 账户关联

所有与京东API交互的实体都必须关联到`JingdongCloudTradeBundle\Entity\Account`实体，以确保：
- 获取正确的授权凭证
- 区分不同商户的数据
- 追踪API调用记录

关联方式示例：
```php
#[ORM\ManyToOne(targetEntity: Account::class)]
#[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false)]
private Account $account;
```

### 订单相关

- `Order` - 订单主体，记录订单基本信息
  - 对应API: [订单查询API](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16491&apiName=jingdong.ctp.order.query.getOrderDetail)
  - 与`Account`实体关联，表示订单所属的京东账户
- `OrderItem` - 订单商品项，记录订单中的商品信息
  - 对应API: [订单商品信息API](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16491&apiName=jingdong.ctp.order.query.getOrderDetail)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`
- `Payment` - 支付信息，记录订单支付相关数据
  - 对应API: [支付结果查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16506&apiName=jingdong.ctp.pay.queryPayResult)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`
- `Invoice` - 发票信息，记录订单发票数据
  - 对应API: [发票信息查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16430&apiName=jingdong.ctp.invoice.getInvoiceDetail)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`

### 商品相关

- `Sku` - 商品信息，记录京东商品数据
  - 对应API: [商品详情查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16478&apiName=jingdong.ctp.sku.getSkuDetail)
  - 与`Account`实体关联，表示SKU所属的京东账户
- `Category` - 商品分类信息
  - 对应API: [商品分类查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16443&apiName=jingdong.ctp.sku.getCategoryDetail)
  - 与`Account`实体关联，表示分类所属的京东账户
- `Brand` - 品牌信息
  - 对应API: [品牌查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16442&apiName=jingdong.ctp.sku.getBrandsByCid)
  - 与`Account`实体关联，表示品牌所属的京东账户

### 物流相关

- `Logistics` - 物流信息，记录订单物流信息
  - 对应API: [物流信息查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16420&apiName=jingdong.ctp.logistics.getTrackInfo)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`
- `DeliveryAddress` - 收货地址，记录用户收货地址
  - 对应API: [地址管理](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16479&apiName=jingdong.ctp.order.getChildAreaList)
  - 与`Account`实体关联，表示地址所属的京东账户
- `Warehouse` - 仓库信息，记录京东仓库信息
  - 对应API: [仓库信息查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16427&apiName=jingdong.ctp.wareHouse.getWareHousesByOrder)
  - 与`Account`实体关联，表示仓库所属的京东账户

### 售后相关

- `AfsService` - 售后服务单，记录售后服务申请信息
  - 对应API: [售后服务单查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16510&apiName=jingdong.ctp.afs.servicebill.getAfsServiceDetail)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`
- `Comment` - 订单评论，记录用户对订单的评价
  - 对应API: [评价查询](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16439&apiName=jingdong.ctp.comment.querySkuComments)
  - 与`Order`实体关联，通过`Order`间接关联到`Account`

## 枚举类

所有枚举类遵循PHP 8.1后原生枚举实现，并实现以下接口：
- `Tourze\EnumExtra\Labelable` - 标签接口
- `Tourze\EnumExtra\Itemable` - 项目接口
- `Tourze\EnumExtra\Selectable` - 选择器接口

### 订单相关枚举

- `OrderStateEnum` - 订单状态
  - 文档: [订单状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16491&apiName=jingdong.ctp.order.query.getOrderDetail)
- `PayTypeEnum` - 支付类型
  - 文档: [支付类型说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16506&apiName=jingdong.ctp.pay.queryPayResult)
- `ItemStateEnum` - 订单商品状态
  - 文档: [订单商品状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16491&apiName=jingdong.ctp.order.query.getOrderDetail)

### 商品相关枚举

- `SkuStateEnum` - 商品状态
  - 文档: [商品状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16478&apiName=jingdong.ctp.sku.getSkuDetail)
- `CategoryStateEnum` - 分类状态
  - 文档: [分类状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16443&apiName=jingdong.ctp.sku.getCategoryDetail)

### 发票相关枚举

- `InvoiceTypeEnum` - 发票类型
  - 文档: [发票类型说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16430&apiName=jingdong.ctp.invoice.getInvoiceDetail)
- `InvoiceTitleTypeEnum` - 发票抬头类型
  - 文档: [发票抬头类型说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16430&apiName=jingdong.ctp.invoice.getInvoiceDetail)
- `InvoiceStateEnum` - 发票状态
  - 文档: [发票状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16430&apiName=jingdong.ctp.invoice.getInvoiceDetail)
- `InvoiceContentEnum` - 发票内容类型
  - 文档: [发票内容类型说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16430&apiName=jingdong.ctp.invoice.getInvoiceDetail)

### 售后相关枚举

- `AfsTypeEnum` - 售后服务类型
  - 文档: [售后类型说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16510&apiName=jingdong.ctp.afs.servicebill.getAfsServiceDetail)
- `AfsServiceStateEnum` - 售后服务状态
  - 文档: [售后状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16510&apiName=jingdong.ctp.afs.servicebill.getAfsServiceDetail)

### 支付相关枚举

- `PaymentMethodEnum` - 支付方式
  - 文档: [支付方式说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16506&apiName=jingdong.ctp.pay.queryPayResult)
- `PaymentChannelEnum` - 支付渠道
  - 文档: [支付渠道说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16506&apiName=jingdong.ctp.pay.queryPayResult)
- `PaymentStateEnum` - 支付状态
  - 文档: [支付状态说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16506&apiName=jingdong.ctp.pay.queryPayResult)

### 评价相关枚举

- `ScoreEnum` - 评分等级
  - 文档: [评分等级说明](https://open.jd.com/home/home#/doc/api?apiCateId=881&apiId=16439&apiName=jingdong.ctp.comment.querySkuComments)

## 消息订阅

本Bundle实现了京东云交易平台消息推送的订阅功能，支持以下事件：

- 商品相关
  - `ct_sku_change` - 商品信息变更
    - 消息文档: [商品信息变更消息](https://open.jd.com/home/home#/doc/msgApi?apiCateId=92&apiId=178)
  - `ct_sku_status` - 商品上下架变更
    - 消息文档: [商品上下架变更消息](https://open.jd.com/home/home/#/doc/msgApi?apiCateId=92&apiId=301)
  - `ct_sku_price_change` - 商品价格变更
    - 消息文档: [商品价格变更消息](https://open.jd.com/home/home#/doc/msgApi?apiCateId=92&apiId=196)
  - `ct_sku_promo_change` - 商品促销变更
    - 消息文档: [商品促销变更消息](https://open.jd.com/home/home/#/doc/msgApi?apiCateId=92&apiId=263)

- 订单相关
  - `ct_order_create` - 订单创建
    - 消息文档: [订单创建消息](https://open.jd.com/home/home#/doc/msgApi?apiCateId=92&apiId=171)
  - `ct_order_pay` - 订单支付
    - 消息文档: [订单支付消息](https://open.jd.com/home/home/#/doc/msgApi?apiCateId=92&apiId=172)
  - `ct_order_stockout` - 订单出库
    - 消息文档: [订单出库消息](https://open.jd.com/home/home#/doc/msgApi?apiCateId=92&apiId=177)

- 售后相关
  - `ct_afs_create` - 售后单创建
    - 消息文档: [售后单创建消息](https://open.jd.com/home/home#/doc/msgApi?apiCateId=92&apiId=221)
  - `ct_afs_state_change` - 售后单状态变更
    - 消息文档: [售后单状态变更消息](https://open.jd.com/home/home#/doc/msgApi?apiCateId=92&apiId=222)

## 技术限制

1. 京东云交易API调用频率限制为60次/分钟
2. 消息推送需配置白名单IP才能接收
3. 全球购商品需单独处理清关信息
4. 接口调用超时时间建议设置为5秒

## 接口调用示例

### 订单查询示例

```php
// 订单查询示例
use JingdongCloudTradeBundle\Repository\AccountRepository;use JingdongCloudTradeBundle\Repository\OrderRepository;

// 获取京东账户
$accountRepository = $container->get(AccountRepository::class);
$account = $accountRepository->findOneBy(['appKey' => 'YOUR_APP_KEY']);

// 查询指定账户下的订单
$orderRepository = $container->get(OrderRepository::class);
$order = $orderRepository->findByOrderIdAndAccount('JD123456789', $account);

// 获取订单商品
$orderItems = $order->getOrderItems();
```

### 商品查询示例

```php
// 商品查询示例
use JingdongCloudTradeBundle\Repository\AccountRepository;use JingdongCloudTradeBundle\Repository\SkuRepository;

// 获取京东账户
$accountRepository = $container->get(AccountRepository::class);
$account = $accountRepository->findOneBy(['appKey' => 'YOUR_APP_KEY']);

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
$afsService = $afsServiceRepository->findByAfsIdAndAccount('AFS123456789', $order->getAccount());
```
