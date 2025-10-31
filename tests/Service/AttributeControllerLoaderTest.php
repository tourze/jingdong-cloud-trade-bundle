<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    private AttributeControllerLoader $loader;

    protected function onSetUp(): void
    {
        $this->loader = self::getService(AttributeControllerLoader::class);
    }

    public function testSupports(): void
    {
        $this->assertFalse($this->loader->supports('resource'));
        $this->assertFalse($this->loader->supports('resource', 'type'));
    }

    public function testLoad(): void
    {
        $collection = $this->loader->load('resource');

        $this->assertInstanceOf(RouteCollection::class, $collection);
    }

    public function testAutoload(): void
    {
        $collection = $this->loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $collection);

        // 验证路由是否正确加载
        $routes = $collection->all();

        // 应该包含至少两个路由（OAuth 相关的路由）
        $this->assertGreaterThanOrEqual(2, count($routes));

        // 检查是否有预期的路由名称
        $routeNames = array_keys($routes);
        $hasOAuthRoute = false;
        foreach ($routeNames as $routeName) {
            if (str_contains($routeName, 'oauth')) {
                $hasOAuthRoute = true;
                break;
            }
        }
        $this->assertTrue($hasOAuthRoute, 'Should contain OAuth related routes');
    }
}
