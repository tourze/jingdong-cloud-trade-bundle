<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\DTO\SyncOptions;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use JingdongCloudTradeBundle\Service\SkuDataProcessor;
use JingdongCloudTradeBundle\Service\SkuService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SkuDataProcessor::class)]
final class SkuDataProcessorTest extends TestCase
{
    private SkuDataProcessor $skuDataProcessor;

    /** @var SkuRepository&MockObject */
    private SkuRepository $skuRepository;

    /** @var SkuService&MockObject */
    private SkuService $skuService;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skuRepository = $this->createMock(SkuRepository::class);
        $this->skuService = $this->createMock(SkuService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->skuDataProcessor = new SkuDataProcessor(
            $this->skuRepository,
            $this->skuService,
            $this->entityManager
        );
    }

    public function testProcessSkuWithValidData(): void
    {
        $account = new Account();
        $skuData = [
            'skuId' => 'SKU123',
            'skuName' => 'Test Product',
            'price' => 100.50,
            'marketPrice' => 120.00,
            'stockNum' => 50,
        ];
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->with('SKU123')
            ->willReturn(null)
        ;

        $this->skuService
            ->expects($this->exactly(2))
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertTrue($result);
    }

    public function testProcessSkuWithMissingSkuId(): void
    {
        $account = new Account();
        $skuData = ['skuName' => 'Test Product']; // Missing skuId
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->never())
            ->method('findBySkuId')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertFalse($result);
    }

    public function testProcessSkuWithEmptySkuId(): void
    {
        $account = new Account();
        $skuData = ['skuId' => '']; // Empty skuId
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->never())
            ->method('findBySkuId')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertFalse($result);
    }

    public function testProcessSkuWithInvalidSkuId(): void
    {
        $account = new Account();
        $skuData = ['skuId' => 123]; // Non-string skuId
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->never())
            ->method('findBySkuId')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertFalse($result);
    }

    public function testProcessSkuCreatesNewSkuWhenNotFound(): void
    {
        $account = new Account();
        $skuData = ['skuId' => 'NEW_SKU'];
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->with('NEW_SKU')
            ->willReturn(null)
        ;

        $this->skuService
            ->expects($this->exactly(2))
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertTrue($result);
    }

    public function testProcessSkuUsesExistingSkuWhenFound(): void
    {
        $account = new Account();
        $skuData = ['skuId' => 'EXISTING_SKU'];
        $options = SyncOptions::fromArray(['force' => true]);

        $existingSku = new Sku();
        $existingSku->getBaseInfo()->setSkuId('EXISTING_SKU');

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->with('EXISTING_SKU')
            ->willReturn($existingSku)
        ;

        $this->skuService
            ->expects($this->exactly(2))
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertTrue($result);
    }

    public function testProcessSkuSkipsWhenShouldNotUpdate(): void
    {
        $account = new Account();
        $skuData = ['skuId' => 'RECENT_SKU'];
        $options = SyncOptions::fromArray(['force' => false]);

        $existingSku = new Sku();
        $existingSku->getBaseInfo()->setSkuId('RECENT_SKU');
        // Set update time to very recent (less than 1 day ago)
        $recentTime = new \DateTimeImmutable('now');
        $reflectionProperty = new \ReflectionProperty($existingSku, 'updateTime');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($existingSku, $recentTime);

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->with('RECENT_SKU')
            ->willReturn($existingSku)
        ;

        $this->skuService
            ->expects($this->never())
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertFalse($result);
    }

    public function testProcessSkuWithCompleteData(): void
    {
        $account = new Account();
        $skuData = [
            'skuId' => 'COMPLETE_SKU',
            'skuName' => 'Complete Product',
            'price' => 99.99,
            'marketPrice' => 129.99,
            'category3Id' => 'CAT123',
            'category3Name' => 'Electronics',
            'brandId' => 'BRAND456',
            'brandName' => 'TechBrand',
            'imageUrl' => 'https://example.com/image.jpg',
            'stockNum' => 100,
            'weight' => 500,
            'detailImages' => 'img1.jpg,img2.jpg,img3.jpg',
            'score' => '4.5',
            'commentCount' => 250,
            'promoInfo' => [
                1 => ['type' => 'discount', 'value' => 10],
                2 => ['type' => 'gift', 'value' => 'free shipping'],
            ],
            'warehouseId' => 'WH001',
            'warehouseName' => 'Main Warehouse',
            'deliveryAreas' => ['beijing' => true, 'shanghai' => true],
            'isGlobalBuy' => true,
            'originCountry' => 'China',
        ];
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->with('COMPLETE_SKU')
            ->willReturn(null)
        ;

        $this->skuService
            ->expects($this->exactly(2))
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertTrue($result);
    }

    /**
     * @param array<string, mixed> $skuData
     */
    #[DataProvider('invalidDataProvider')]
    public function testProcessSkuHandlesInvalidData(array $skuData): void
    {
        $account = new Account();
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->willReturn(null)
        ;

        $this->skuService
            ->expects($this->exactly(2))
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertTrue($result);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function invalidDataProvider(): array
    {
        return [
            'invalid_stock_type' => [
                ['skuId' => 'TEST_SKU', 'stockNum' => 'invalid'],
            ],
            'invalid_score_type' => [
                ['skuId' => 'TEST_SKU', 'score' => 123],
            ],
            'invalid_comment_count' => [
                ['skuId' => 'TEST_SKU', 'commentCount' => 'invalid'],
            ],
            'invalid_detail_images' => [
                ['skuId' => 'TEST_SKU', 'detailImages' => 123],
            ],
            'invalid_warehouse_id' => [
                ['skuId' => 'TEST_SKU', 'warehouseId' => 123],
            ],
            'invalid_delivery_areas' => [
                ['skuId' => 'TEST_SKU', 'deliveryAreas' => 'invalid'],
            ],
            'invalid_global_buy' => [
                ['skuId' => 'TEST_SKU', 'isGlobalBuy' => 'invalid'],
            ],
        ];
    }

    public function testProcessSkuWithStringDetailImages(): void
    {
        $account = new Account();
        $skuData = [
            'skuId' => 'STRING_IMAGES_SKU',
            'detailImages' => 'image1.jpg,image2.jpg,image3.jpg',
        ];
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->willReturn(null)
        ;

        $this->skuService
            ->expects($this->exactly(2))
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertTrue($result);
    }

    public function testProcessSkuWithArrayDetailImages(): void
    {
        $account = new Account();
        $skuData = [
            'skuId' => 'ARRAY_IMAGES_SKU',
            'detailImages' => ['image1.jpg', 'image2.jpg', 123], // Mixed types
        ];
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->willReturn(null)
        ;

        $this->skuService
            ->expects($this->exactly(2))
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertTrue($result);
    }

    public function testProcessSkuWithPromotionInfo(): void
    {
        $account = new Account();
        $skuData = [
            'skuId' => 'PROMO_SKU',
            'promoInfo' => [
                1 => ['discount' => 10],
                'invalid_key' => ['gift' => 'item'], // Should be filtered out
                2 => ['shipping' => 'free'],
            ],
        ];
        $options = SyncOptions::fromArray(['force' => true]);

        $this->skuRepository
            ->expects($this->once())
            ->method('findBySkuId')
            ->willReturn(null)
        ;

        $this->skuService
            ->expects($this->exactly(2))
            ->method('fillSkuFromApiData')
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
        ;

        $result = $this->skuDataProcessor->processSku($account, $skuData, $options);

        $this->assertTrue($result);
    }
}
