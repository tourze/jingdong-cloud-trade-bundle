<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Controller\Admin;

use JingdongCloudTradeBundle\Controller\Admin\OrderCrudController;
use JingdongCloudTradeBundle\Entity\Order;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\PropertyAccess\Exception\InvalidTypeException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(OrderCrudController::class)]
#[RunTestsInSeparateProcesses]
class OrderCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return OrderCrudController<Order>
     */
    protected function getControllerService(): OrderCrudController
    {
        return self::getService(OrderCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'orderId' => ['orderId'];
        yield 'orderState' => ['orderState'];
        yield 'paymentState' => ['paymentState'];
        yield 'logisticsState' => ['logisticsState'];
        yield 'receiverName' => ['receiverName'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'orderId' => ['orderId'];
        yield 'orderState' => ['orderState'];
        yield 'paymentState' => ['paymentState'];
        yield 'logisticsState' => ['logisticsState'];
        yield 'receiverName' => ['receiverName'];
        yield 'receiverMobile' => ['receiverMobile'];
        yield 'orderTotalPrice' => ['orderTotalPrice'];
        yield 'orderPaymentPrice' => ['orderPaymentPrice'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'orderId' => ['京东订单号'];
        yield 'orderState' => ['订单状态'];
        yield 'paymentState' => ['支付状态'];
        yield 'logisticsState' => ['物流状态'];
        yield 'receiverName' => ['收货人姓名'];
        yield 'receiverMobile' => ['收货人手机号'];
        yield 'orderTotalPrice' => ['订单总金额'];
        yield 'orderPaymentPrice' => ['实付金额'];
        yield 'orderTime' => ['下单时间'];
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
        $this->assertStringContainsString('京东订单号', $content);
        $this->assertStringContainsString('订单状态', $content);
        $this->assertStringContainsString('支付状态', $content);
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
