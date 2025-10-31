<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\OrderItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(OrderItem::class)]
final class OrderItemTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        // 创建关联实体
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test_app_key');
        $account->setAppSecret('test_app_secret');

        $order = new Order();
        $order->setOrderId('JD123456789');
        $order->setOrderState('PROCESSING');
        $order->setPaymentState('PAID');
        $order->setLogisticsState('SHIPPED');
        $order->setAccount($account);

        $orderItem = new OrderItem();
        $orderItem->setAccount($account);
        $orderItem->setOrder($order);
        $orderItem->setSkuId('SKU123456');
        $orderItem->setSkuName('测试商品');
        $orderItem->setQuantity(1);
        $orderItem->setPrice('99.99');
        $orderItem->setTotalPrice('99.99');

        return $orderItem;
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'skuId' => ['skuId', 'SKU987654'];
        yield 'skuName' => ['skuName', '另一个测试商品'];
        yield 'quantity' => ['quantity', 3];
        yield 'price' => ['price', '149.99'];
        yield 'totalPrice' => ['totalPrice', '449.97'];
        yield 'imageUrl' => ['imageUrl', 'https://example.com/image2.jpg'];
        yield 'attributes' => ['attributes', '{"color":"蓝色","size":"L"}'];
    }

    /**
     * 测试 retrievePlainArray 方法
     */
    public function testRetrievePlainArray(): void
    {
        // 创建关联实体
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test_app_key');
        $account->setAppSecret('test_app_secret');

        $order = new Order();
        $order->setOrderId('JD123456789');
        $order->setOrderState('PROCESSING');
        $order->setPaymentState('PAID');
        $order->setLogisticsState('SHIPPED');
        $order->setAccount($account);

        $orderItem = new OrderItem();
        $orderItem->setAccount($account);
        $orderItem->setOrder($order);
        $orderItem->setSkuId('SKU123456');
        $orderItem->setSkuName('测试商品');
        $orderItem->setQuantity(2);
        $orderItem->setPrice('99.99');
        $orderItem->setTotalPrice('199.98');
        $orderItem->setImageUrl('https://example.com/img.jpg');
        $orderItem->setAttributes('{"color":"红色","size":"M"}');

        $plainArray = $orderItem->retrievePlainArray();

        $this->assertArrayHasKey('id', $plainArray);
        $this->assertArrayHasKey('orderId', $plainArray);
        $this->assertArrayHasKey('skuId', $plainArray);
        $this->assertArrayHasKey('skuName', $plainArray);
        $this->assertArrayHasKey('quantity', $plainArray);
        $this->assertArrayHasKey('price', $plainArray);
        $this->assertArrayHasKey('totalPrice', $plainArray);
        $this->assertArrayHasKey('imageUrl', $plainArray);
        $this->assertArrayHasKey('attributes', $plainArray);
        $this->assertArrayHasKey('accountId', $plainArray);
        $this->assertArrayHasKey('createTime', $plainArray);
        $this->assertArrayHasKey('updateTime', $plainArray);

        $this->assertEquals('SKU123456', $plainArray['skuId']);
        $this->assertEquals('测试商品', $plainArray['skuName']);
        $this->assertEquals(2, $plainArray['quantity']);
        $this->assertEquals('99.99', $plainArray['price']);
        $this->assertEquals('199.98', $plainArray['totalPrice']);
        $this->assertEquals('https://example.com/img.jpg', $plainArray['imageUrl']);
        $this->assertEquals('{"color":"红色","size":"M"}', $plainArray['attributes']);
    }

    /**
     * 测试 __toString 方法
     */
    public function testToString(): void
    {
        // 创建关联实体
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test_app_key');
        $account->setAppSecret('test_app_secret');

        $order = new Order();
        $order->setOrderId('JD123456789');
        $order->setOrderState('PROCESSING');
        $order->setPaymentState('PAID');
        $order->setLogisticsState('SHIPPED');
        $order->setAccount($account);

        $orderItem = new OrderItem();
        $orderItem->setAccount($account);
        $orderItem->setOrder($order);
        $orderItem->setSkuId('SKU123456');
        $orderItem->setSkuName('测试商品');
        $orderItem->setQuantity(2);
        $orderItem->setPrice('99.99');
        $orderItem->setTotalPrice('199.98');

        $this->assertEquals('测试商品 x2', (string) $orderItem);
    }
}
