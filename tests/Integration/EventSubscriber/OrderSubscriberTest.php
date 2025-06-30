<?php

namespace JingdongCloudTradeBundle\Tests\Integration\EventSubscriber;

use JingdongCloudTradeBundle\EventSubscriber\OrderSubscriber;
use JingdongCloudTradeBundle\Service\Client;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class OrderSubscriberTest extends TestCase
{
    public function testSubscriberClass(): void
    {
        $this->assertTrue(class_exists(OrderSubscriber::class));
        
        $client = $this->createMock(Client::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $subscriber = new OrderSubscriber($client, $logger);
        $this->assertInstanceOf(OrderSubscriber::class, $subscriber);
    }
}