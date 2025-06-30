<?php

namespace JingdongCloudTradeBundle\Tests\Unit\DependencyInjection;

use JingdongCloudTradeBundle\DependencyInjection\JingdongCloudTradeExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JingdongCloudTradeExtensionTest extends TestCase
{
    private JingdongCloudTradeExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new JingdongCloudTradeExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $this->extension->load([], $this->container);
        
        // 验证容器已加载配置，而不是具体的服务定义（因为是自动扫描的）
        $this->assertNotEmpty($this->container->getDefinitions());
    }
}