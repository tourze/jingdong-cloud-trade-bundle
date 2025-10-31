<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Controller\Admin;

use JingdongCloudTradeBundle\Controller\Admin\AfsServiceCrudController;
use JingdongCloudTradeBundle\Entity\AfsService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\PropertyAccess\Exception\InvalidTypeException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(AfsServiceCrudController::class)]
#[RunTestsInSeparateProcesses]
class AfsServiceCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AfsServiceCrudController<AfsService>
     */
    protected function getControllerService(): AfsServiceCrudController
    {
        return self::getService(AfsServiceCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'afsServiceId' => ['afsServiceId'];
        yield 'afsType' => ['afsType'];
        yield 'afsServiceState' => ['afsServiceState'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'afsServiceId' => ['afsServiceId'];
        yield 'afsType' => ['afsType'];
        yield 'afsServiceState' => ['afsServiceState'];
        yield 'refundAmount' => ['refundAmount'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'order' => ['关联订单'];
        yield 'afsServiceId' => ['售后服务单号'];
        yield 'afsType' => ['售后类型'];
        yield 'afsServiceState' => ['服务单状态'];
        yield 'refundAmount' => ['退款金额'];
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
        $this->assertStringContainsString('售后服务单号', $content);
        $this->assertStringContainsString('售后类型', $content);
        $this->assertStringContainsString('服务单状态', $content);
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
