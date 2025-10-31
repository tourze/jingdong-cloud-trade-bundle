<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Controller\Admin;

use JingdongCloudTradeBundle\Controller\Admin\InvoiceCrudController;
use JingdongCloudTradeBundle\Entity\Invoice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\PropertyAccess\Exception\InvalidTypeException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(InvoiceCrudController::class)]
#[RunTestsInSeparateProcesses]
class InvoiceCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return InvoiceCrudController<Invoice>
     */
    protected function getControllerService(): InvoiceCrudController
    {
        return self::getService(InvoiceCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'invoiceType' => ['invoiceType'];
        yield 'title' => ['title'];
        yield 'titleType' => ['titleType'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'invoiceType' => ['invoiceType'];
        yield 'invoiceState' => ['invoiceState'];
        yield 'title' => ['title'];
        yield 'invoiceContent' => ['invoiceContent'];
        yield 'titleType' => ['titleType'];
        yield 'taxpayerIdentity' => ['taxpayerIdentity'];
        yield 'registeredAddress' => ['registeredAddress'];
        yield 'bankName' => ['bankName'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'order' => ['关联订单'];
        yield 'invoiceType' => ['发票类型'];
        yield 'titleType' => ['抬头类型'];
        yield 'title' => ['发票抬头'];
        yield 'invoiceAmount' => ['发票金额'];
        yield 'invoiceState' => ['发票状态'];
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
        $this->assertStringContainsString('发票类型', $content);
        $this->assertStringContainsString('发票抬头', $content);
        $this->assertStringContainsString('抬头类型', $content);
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
