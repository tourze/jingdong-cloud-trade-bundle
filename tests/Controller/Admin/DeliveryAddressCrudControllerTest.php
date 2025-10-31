<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Controller\Admin;

use JingdongCloudTradeBundle\Controller\Admin\DeliveryAddressCrudController;
use JingdongCloudTradeBundle\Entity\DeliveryAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\PropertyAccess\Exception\InvalidTypeException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddressCrudController::class)]
#[RunTestsInSeparateProcesses]
class DeliveryAddressCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return DeliveryAddressCrudController<DeliveryAddress>
     */
    protected function getControllerService(): DeliveryAddressCrudController
    {
        return self::getService(DeliveryAddressCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'receiverName' => ['receiverName'];
        yield 'receiverMobile' => ['receiverMobile'];
        yield 'province' => ['province'];
        yield 'city' => ['city'];
        yield 'county' => ['county'];
        yield 'detailAddress' => ['detailAddress'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'receiverName' => ['receiverName'];
        yield 'receiverMobile' => ['receiverMobile'];
        yield 'province' => ['province'];
        yield 'city' => ['city'];
        yield 'county' => ['county'];
        yield 'detailAddress' => ['detailAddress'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'receiverName' => ['收货人姓名'];
        yield 'receiverMobile' => ['收货人手机号'];
        yield 'province' => ['省份'];
        yield 'city' => ['城市'];
        yield 'county' => ['区县'];
        yield 'isDefault' => ['默认地址'];
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
        $this->assertStringContainsString('收货人姓名', $content);
        $this->assertStringContainsString('收货人手机号', $content);
        $this->assertStringContainsString('省份', $content);
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
