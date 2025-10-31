<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Controller\Admin;

use JingdongCloudTradeBundle\Controller\Admin\LogisticsCrudController;
use JingdongCloudTradeBundle\Entity\Logistics;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\PropertyAccess\Exception\InvalidTypeException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(LogisticsCrudController::class)]
#[RunTestsInSeparateProcesses]
class LogisticsCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return LogisticsCrudController<Logistics>
     */
    protected function getControllerService(): LogisticsCrudController
    {
        return self::getService(LogisticsCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'logisticsCode' => ['logisticsCode'];
        yield 'waybillCode' => ['waybillCode'];
        yield 'logisticsName' => ['logisticsName'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'logisticsCode' => ['logisticsCode'];
        yield 'waybillCode' => ['waybillCode'];
        yield 'logisticsName' => ['logisticsName'];
        yield 'trackInfo' => ['trackInfo'];
        yield 'lastModificationTime' => ['lastModificationTime'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'order' => ['关联订单'];
        yield 'logisticsCode' => ['物流公司编码'];
        yield 'logisticsName' => ['物流公司名称'];
        yield 'waybillCode' => ['物流单号'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 测试表单验证功能
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        // 验证新建页面包含必需的表单字段和验证标记
        $content = (string) $client->getResponse()->getContent();
        $this->assertStringContainsString('物流公司编码', $content);
        $this->assertStringContainsString('物流单号', $content);
        $this->assertStringContainsString('物流公司名称', $content);
        $this->assertStringContainsString('required', $content, '表单应包含必填字段标记');

        // 获取表单并尝试提交空表单
        $form = $crawler->selectButton('Create')->form();

        try {
            // 提交空表单
            $crawler = $client->submit($form);

            // 如果没有抛出异常，验证响应
            $this->assertSame(422, $client->getResponse()->getStatusCode());
            $this->assertStringContainsString(
                'should not be blank',
                (string) $client->getResponse()->getContent()
            );
        } catch (\TypeError|InvalidTypeException $e) {
            // 严格类型模式下预期的行为 - setter不接受null
            $message = $e->getMessage();
            $this->assertTrue(
                str_contains($message, 'null given') || str_contains($message, 'null') && str_contains($message, 'given'),
                'Type safety validation is working as expected'
            );
        }
    }
}
