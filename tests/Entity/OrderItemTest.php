<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    private OrderItem $orderItem;
    private Order $order;
    private Account $account;

    protected function setUp(): void
    {
        $this->orderItem = new OrderItem();
        $this->order = new Order();
        $this->account = new Account();
        
        // 设置必要的值
        $this->account->setName('Test Account');
        $this->account->setAppKey('test_app_key');
        $this->account->setAppSecret('test_app_secret');
        
        $this->order->setAccount($this->account);
        $this->order->setOrderId('JD123456789');
        $this->order->setOrderState('CREATED');
        $this->order->setPaymentState('UNPAID');
        $this->order->setLogisticsState('UNSHIPPED');
        $this->order->setReceiverName('测试用户');
        $this->order->setReceiverMobile('13800138000');
        $this->order->setReceiverProvince('北京市');
        $this->order->setReceiverCity('北京市');
        $this->order->setReceiverCounty('海淀区');
        $this->order->setReceiverAddress('测试地址');
        $this->order->setOrderTotalPrice('299.99');
        $this->order->setOrderPaymentPrice('289.99');
        $this->order->setFreightPrice('10.00');
        $this->order->setOrderTime(new \DateTimeImmutable());
    }
    
    public function testBasicProperties(): void
    {
        $skuId = 'SKU123456';
        $skuName = '测试商品';
        $quantity = 2;
        $price = '99.99';
        $totalPrice = '199.98';
        $imageUrl = 'https://example.com/img.jpg';
        $attributes = '{"color":"红色","size":"M"}';
        
        $this->orderItem->setSkuId($skuId);
        $this->orderItem->setSkuName($skuName);
        $this->orderItem->setQuantity($quantity);
        $this->orderItem->setPrice($price);
        $this->orderItem->setTotalPrice($totalPrice);
        $this->orderItem->setImageUrl($imageUrl);
        $this->orderItem->setAttributes($attributes);
        
        $this->assertSame($skuId, $this->orderItem->getSkuId());
        $this->assertSame($skuName, $this->orderItem->getSkuName());
        $this->assertSame($quantity, $this->orderItem->getQuantity());
        $this->assertSame($price, $this->orderItem->getPrice());
        $this->assertSame($totalPrice, $this->orderItem->getTotalPrice());
        $this->assertSame($imageUrl, $this->orderItem->getImageUrl());
        $this->assertSame($attributes, $this->orderItem->getAttributes());
    }
    
    public function testOrderRelation(): void
    {
        $this->orderItem->setOrder($this->order);
        
        $this->assertSame($this->order, $this->orderItem->getOrder());
    }
    
    public function testAccountRelation(): void
    {
        $this->orderItem->setAccount($this->account);
        
        $this->assertSame($this->account, $this->orderItem->getAccount());
    }
    
    public function testTimestampProperties(): void
    {
        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();
        
        $this->orderItem->setCreateTime($createTime);
        $this->orderItem->setUpdateTime($updateTime);
        
        $this->assertSame($createTime, $this->orderItem->getCreateTime());
        $this->assertSame($updateTime, $this->orderItem->getUpdateTime());
    }
    
    public function testRetrievePlainArray(): void
    {
        // 设置必要的ID值模拟数据库中的对象
        $this->setPrivateProperty($this->orderItem, 'id', 1);
        $this->setPrivateProperty($this->order, 'id', 100);
        $this->setPrivateProperty($this->account, 'id', 200);
        
        $skuId = 'SKU123456';
        $skuName = '测试商品';
        $quantity = 2;
        $price = '99.99';
        $totalPrice = '199.98';
        $imageUrl = 'https://example.com/img.jpg';
        $attributes = '{"color":"红色","size":"M"}';
        $createTime = new \DateTimeImmutable('2023-01-01 10:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 11:00:00');
        
        $this->orderItem->setOrder($this->order);
        $this->orderItem->setAccount($this->account);
        $this->orderItem->setSkuId($skuId);
        $this->orderItem->setSkuName($skuName);
        $this->orderItem->setQuantity($quantity);
        $this->orderItem->setPrice($price);
        $this->orderItem->setTotalPrice($totalPrice);
        $this->orderItem->setImageUrl($imageUrl);
        $this->orderItem->setAttributes($attributes);
        $this->orderItem->setCreateTime($createTime);
        $this->orderItem->setUpdateTime($updateTime);
        
        $plainArray = $this->orderItem->retrievePlainArray();
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
        
        $this->assertEquals(1, $plainArray['id']);
        $this->assertEquals(100, $plainArray['orderId']);
        $this->assertEquals($skuId, $plainArray['skuId']);
        $this->assertEquals($skuName, $plainArray['skuName']);
        $this->assertEquals($quantity, $plainArray['quantity']);
        $this->assertEquals($price, $plainArray['price']);
        $this->assertEquals($totalPrice, $plainArray['totalPrice']);
        $this->assertEquals($imageUrl, $plainArray['imageUrl']);
        $this->assertEquals($attributes, $plainArray['attributes']);
        $this->assertEquals(200, $plainArray['accountId']);
        $this->assertEquals('2023-01-01 10:00:00', $plainArray['createTime']);
        $this->assertEquals('2023-01-02 11:00:00', $plainArray['updateTime']);
    }
    
    /**
     * 通过反射设置私有属性的值
     */
    private function setPrivateProperty(object $object, string $propertyName, mixed $value): void
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
} 