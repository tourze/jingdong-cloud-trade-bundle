<?php

namespace JingdongCloudTradeBundle\Tests\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Service\DataProcessor\ArrayDataValidator;
use JingdongCloudTradeBundle\Service\DataProcessor\BaseInfoFillStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BaseInfoFillStrategy::class)]
final class BaseInfoFillStrategyTest extends TestCase
{
    private BaseInfoFillStrategy $strategy;

    private ArrayDataValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ArrayDataValidator();
        $this->strategy = new BaseInfoFillStrategy($this->validator);
    }

    public function testCanHandleReturnsTrueForValidSection(): void
    {
        $data = [
            'skuBaseInfo' => [
                'skuId' => '123456',
                'skuName' => 'Test Product',
            ],
        ];

        $this->assertTrue($this->strategy->canHandle($data, 'skuBaseInfo'));
    }

    public function testCanHandleReturnsFalseForInvalidSection(): void
    {
        $data = [
            'skuBaseInfo' => [
                'skuId' => '123456',
            ],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'imageInfos'));
    }

    public function testCanHandleReturnsFalseForMissingSection(): void
    {
        $data = [
            'otherField' => ['value'],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'skuBaseInfo'));
    }

    public function testCanHandleReturnsFalseForNonArraySection(): void
    {
        $data = [
            'skuBaseInfo' => 'not an array',
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'skuBaseInfo'));
    }

    public function testFillBasicInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'skuId' => '123456',
                'skuName' => 'Test Product',
                'price' => '99.99',
                'marketPrice' => '199.99',
                'venderName' => 'Test Vendor',
                'shopName' => 'Test Shop',
                'delivery' => 'Fast Delivery',
                'unit' => '个',
                'model' => 'MODEL-001',
                'color' => 'Red',
                'size' => 'Large',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBaseInfo');

        $baseInfo = $sku->getBaseInfo();
        $this->assertSame('123456', $baseInfo->getSkuId());
        $this->assertSame('Test Product', $baseInfo->getSkuName());
        $this->assertSame('99.99', $baseInfo->getPrice());
        $this->assertSame('199.99', $baseInfo->getMarketPrice());
        $this->assertSame('Test Vendor', $baseInfo->getVendorName());
        $this->assertSame('Test Shop', $baseInfo->getShopName());
        $this->assertSame('Fast Delivery', $baseInfo->getDelivery());
        $this->assertSame('个', $baseInfo->getUnit());
        $this->assertSame('MODEL-001', $baseInfo->getModel());
        $this->assertSame('Red', $baseInfo->getColor());
        $this->assertSame('Large', $baseInfo->getSize());
    }

    public function testFillCategoryInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'categoryId' => '1000',
                'categoryName' => 'Electronics',
                'categoryId1' => '10',
                'categoryName1' => 'Tech',
                'categoryId2' => '100',
                'categoryName2' => 'Computers',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBaseInfo');

        $baseInfo = $sku->getBaseInfo();
        $this->assertSame('1000', $baseInfo->getCategoryId());
        $this->assertSame('Electronics', $baseInfo->getCategoryName());
        $this->assertSame('10', $baseInfo->getCategoryId1());
        $this->assertSame('Tech', $baseInfo->getCategoryName1());
        $this->assertSame('100', $baseInfo->getCategoryId2());
        $this->assertSame('Computers', $baseInfo->getCategoryName2());
    }

    public function testFillBrandInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'brandId' => '5000',
                'brandName' => 'Test Brand',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBaseInfo');

        $baseInfo = $sku->getBaseInfo();
        $this->assertSame('5000', $baseInfo->getBrandId());
        $this->assertSame('Test Brand', $baseInfo->getBrandName());
    }

    public function testFillNumericFields(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'weight' => '1000',
                'width' => '10.5',
                'height' => '20.8',
                'length' => '30.2',
                'shelfLife' => '365',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBaseInfo');

        $baseInfo = $sku->getBaseInfo();
        $this->assertSame(1000, $baseInfo->getWeight());
        $this->assertSame(10.5, $baseInfo->getWidth());
        $this->assertSame(20.8, $baseInfo->getHeight());
        $this->assertSame(30.2, $baseInfo->getLength());
        $this->assertSame(365, $baseInfo->getShelfLife());
    }

    public function testFillSaleAttributes(): void
    {
        $sku = new Sku();
        $saleAttrs = [
            ['name' => '颜色', 'value' => '红色'],
            ['name' => '尺寸', 'value' => 'M'],
        ];
        $data = [
            'skuBaseInfo' => [
                'saleAttributesList' => $saleAttrs,
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBaseInfo');

        $baseInfo = $sku->getBaseInfo();
        $this->assertSame($saleAttrs, $baseInfo->getSaleAttrs());
    }

    public function testFillIsGlobalBuyForWareType2(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'wareType' => '2',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBaseInfo');

        $baseInfo = $sku->getBaseInfo();
        $this->assertTrue($baseInfo->isGlobalBuy());
    }

    public function testFillIsGlobalBuyForNonWareType2(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'wareType' => '1',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBaseInfo');

        $baseInfo = $sku->getBaseInfo();
        $this->assertFalse($baseInfo->isGlobalBuy());
    }

    public function testFillWithMissingFields(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'skuId' => '123456',
                'brandId' => '5000',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBaseInfo');

        $baseInfo = $sku->getBaseInfo();
        $this->assertSame('123456', $baseInfo->getSkuId());
        $this->assertSame('5000', $baseInfo->getBrandId());
        $this->assertNull($baseInfo->getBrandName());
    }
}
