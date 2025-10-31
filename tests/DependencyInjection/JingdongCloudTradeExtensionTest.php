<?php

namespace JingdongCloudTradeBundle\Tests\DependencyInjection;

use JingdongCloudTradeBundle\DependencyInjection\JingdongCloudTradeExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(JingdongCloudTradeExtension::class)]
final class JingdongCloudTradeExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testGetAliasReturnsCorrectAlias(): void
    {
        $extension = new JingdongCloudTradeExtension();

        // Extension 基类默认会从类名推导出别名
        $expectedAlias = 'jingdong_cloud_trade';
        $this->assertSame($expectedAlias, $extension->getAlias());
    }
}
