<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\DTO\SyncOptions;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Enum\SkuStateEnum;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use JingdongCloudTradeBundle\Service\SkuDataProcessor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SkuDataProcessor::class)]
#[RunTestsInSeparateProcesses]
final class SkuDataProcessorTest extends AbstractIntegrationTestCase
{
    private SkuDataProcessor $skuDataProcessor;

    private Account $testAccount;

    protected function onSetUp(): void
    {
        $this->skuDataProcessor = self::getService(SkuDataProcessor::class);

        // 创建测试账户
        $this->testAccount = new Account();
        $this->testAccount->setAppKey('test-app-key');
        $this->testAccount->setAppSecret('test-app-secret');
        $this->testAccount->setName('Test Account');

        $entityManager = self::getService(EntityManagerInterface::class);
        $entityManager->persist($this->testAccount);
        $entityManager->flush();
    }

    public function testProcessSkuWithValidData(): void
    {
        $skuData = [
            'skuId' => 'SKU123_' . uniqid(),
            'skuName' => 'Test Product',
            'price' => 100.50,
            'marketPrice' => 120.00,
            'stockNum' => 50,
            'state' => SkuStateEnum::ON_SALE,
        ];
        $options = SyncOptions::fromArray(['force' => true]);

        $result = $this->skuDataProcessor->processSku($this->testAccount, $skuData, $options);

        $this->assertTrue($result);
    }

    public function testProcessSkuWithMissingSkuId(): void
    {
        $skuData = ['skuName' => 'Test Product']; // Missing skuId
        $options = SyncOptions::fromArray(['force' => true]);

        $result = $this->skuDataProcessor->processSku($this->testAccount, $skuData, $options);

        $this->assertFalse($result);
    }

    public function testProcessSkuWithEmptySkuId(): void
    {
        $skuData = ['skuId' => '']; // Empty skuId
        $options = SyncOptions::fromArray(['force' => true]);

        $result = $this->skuDataProcessor->processSku($this->testAccount, $skuData, $options);

        $this->assertFalse($result);
    }

    public function testProcessSkuWithInvalidSkuId(): void
    {
        $skuData = ['skuId' => 123]; // Non-string skuId
        $options = SyncOptions::fromArray(['force' => true]);

        $result = $this->skuDataProcessor->processSku($this->testAccount, $skuData, $options);

        $this->assertFalse($result);
    }

    public function testProcessSkuWithForceOptionFalseAndRecentUpdate(): void
    {
        $skuId = 'RECENT_SKU_' . uniqid();
        $skuData = [
            'skuId' => $skuId,
            'skuName' => 'Updated Product',
            'state' => SkuStateEnum::ON_SALE,
        ];
        $options = SyncOptions::fromArray(['force' => false]);

        // Create an existing SKU that was recently updated
        $result = $this->skuDataProcessor->processSku($this->testAccount, $skuData, $options);
        $this->assertTrue($result); // First time should always work

        // Try to update with force=false and recent timestamp
        $recentTime = new \DateTimeImmutable('now');
        $entityManager = self::getService(EntityManagerInterface::class);

        // Get the created SKU and update its timestamp
        $skuRepository = self::getService(SkuRepository::class);
        $existingSku = $skuRepository->findBySkuId($skuId);
        $this->assertNotNull($existingSku);

        // Set recent update time using reflection
        $reflectionProperty = new \ReflectionProperty($existingSku, 'updateTime');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($existingSku, $recentTime);

        $entityManager->persist($existingSku);
        $entityManager->flush();

        // Now try to process again with force=false
        $result = $this->skuDataProcessor->processSku($this->testAccount, $skuData, $options);

        $this->assertFalse($result); // Should not process due to recent update
    }
}
