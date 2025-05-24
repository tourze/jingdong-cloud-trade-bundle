<?php

namespace JingdongCloudTradeBundle\Tests\Enum;

use JingdongCloudTradeBundle\Enum\OrderStateEnum;
use PHPUnit\Framework\TestCase;

class OrderStateEnumTest extends TestCase
{
    public function testOrderStateEnumValues(): void
    {
        $this->assertSame('CREATED', OrderStateEnum::CREATED->value);
        $this->assertSame('PAID', OrderStateEnum::PAID->value);
        $this->assertSame('SHIPPED', OrderStateEnum::SHIPPED->value);
        $this->assertSame('COMPLETED', OrderStateEnum::COMPLETED->value);
        $this->assertSame('CANCELLED', OrderStateEnum::CANCELLED->value);
        $this->assertSame('CLOSED', OrderStateEnum::CLOSED->value);
        $this->assertSame('AFTER_SALE', OrderStateEnum::AFTER_SALE->value);
    }
    
    public function testGetLabel(): void
    {
        $this->assertSame('已创建', OrderStateEnum::CREATED->getLabel());
        $this->assertSame('已支付', OrderStateEnum::PAID->getLabel());
        $this->assertSame('已发货', OrderStateEnum::SHIPPED->getLabel());
        $this->assertSame('已完成', OrderStateEnum::COMPLETED->getLabel());
        $this->assertSame('已取消', OrderStateEnum::CANCELLED->getLabel());
        $this->assertSame('已关闭', OrderStateEnum::CLOSED->getLabel());
        $this->assertSame('售后中', OrderStateEnum::AFTER_SALE->getLabel());
    }
    
    public function testFromValue(): void
    {
        $this->assertSame(OrderStateEnum::CREATED, OrderStateEnum::from('CREATED'));
        $this->assertSame(OrderStateEnum::PAID, OrderStateEnum::from('PAID'));
        $this->assertSame(OrderStateEnum::SHIPPED, OrderStateEnum::from('SHIPPED'));
        $this->assertSame(OrderStateEnum::COMPLETED, OrderStateEnum::from('COMPLETED'));
        $this->assertSame(OrderStateEnum::CANCELLED, OrderStateEnum::from('CANCELLED'));
        $this->assertSame(OrderStateEnum::CLOSED, OrderStateEnum::from('CLOSED'));
        $this->assertSame(OrderStateEnum::AFTER_SALE, OrderStateEnum::from('AFTER_SALE'));
    }
    
    public function testTryFromValue_validValue(): void
    {
        $this->assertSame(OrderStateEnum::CREATED, OrderStateEnum::tryFrom('CREATED'));
        $this->assertSame(OrderStateEnum::PAID, OrderStateEnum::tryFrom('PAID'));
    }
    
    public function testTryFromValue_invalidValue(): void
    {
        $this->assertNull(OrderStateEnum::tryFrom('UNKNOWN_STATE'));
        $this->assertNull(OrderStateEnum::tryFrom(''));
    }

    public function testCases(): void
    {
        $cases = OrderStateEnum::cases();
        
        $this->assertIsArray($cases);
        $this->assertCount(7, $cases);
        
        $this->assertSame(OrderStateEnum::CREATED, $cases[0]);
        $this->assertSame(OrderStateEnum::PAID, $cases[1]);
        $this->assertSame(OrderStateEnum::SHIPPED, $cases[2]);
        $this->assertSame(OrderStateEnum::COMPLETED, $cases[3]);
        $this->assertSame(OrderStateEnum::CANCELLED, $cases[4]);
        $this->assertSame(OrderStateEnum::CLOSED, $cases[5]);
        $this->assertSame(OrderStateEnum::AFTER_SALE, $cases[6]);
    }
    
    public function testToSelectItem(): void
    {
        $selectItem = OrderStateEnum::CREATED->toSelectItem();
        
        $this->assertIsArray($selectItem);
        $this->assertArrayHasKey('label', $selectItem);
        $this->assertArrayHasKey('text', $selectItem);
        $this->assertArrayHasKey('value', $selectItem);
        $this->assertArrayHasKey('name', $selectItem);
        
        $this->assertEquals('已创建', $selectItem['label']);
        $this->assertEquals('已创建', $selectItem['text']);
        $this->assertEquals('CREATED', $selectItem['value']);
        $this->assertEquals('已创建', $selectItem['name']);
    }
    
    public function testToArray(): void
    {
        $array = OrderStateEnum::PAID->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        
        $this->assertEquals('PAID', $array['value']);
        $this->assertEquals('已支付', $array['label']);
    }
    
    public function testGenOptions(): void
    {
        $options = OrderStateEnum::genOptions();
        
        $this->assertIsArray($options);
        $this->assertNotEmpty($options);
        
        foreach ($options as $option) {
            $this->assertIsArray($option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);
            
            $enum = OrderStateEnum::from($option['value']);
            $this->assertSame($enum->getLabel(), $option['label']);
        }
    }
} 