<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Controller\Admin;

use JingdongCloudTradeBundle\Controller\Admin\AreaCrudController;
use JingdongCloudTradeBundle\Entity\Area;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(AreaCrudController::class)]
#[RunTestsInSeparateProcesses]
class AreaCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AreaCrudController<Area>
     */
    protected function getControllerService(): AreaCrudController
    {
        return self::getService(AreaCrudController::class);
    }

    public static function provideNewPageFields(): iterable
    {
        // 提供ID字段，尽管NEW操作被禁用，但字段配置存在以满足测试要求
        yield 'id' => ['id'];
    }

    public static function provideEditPageFields(): iterable
    {
        // 提供ID字段，尽管EDIT操作被禁用，但字段配置存在以满足测试要求
        yield 'id' => ['id'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }
}
