<?php

namespace JingdongCloudTradeBundle\Tests\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Service\DataProcessor\ArrayDataValidator;
use JingdongCloudTradeBundle\Service\DataProcessor\BigFieldFillStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BigFieldFillStrategy::class)]
final class BigFieldFillStrategyTest extends TestCase
{
    private BigFieldFillStrategy $strategy;

    protected function setUp(): void
    {
        $validator = new ArrayDataValidator();
        $this->strategy = new BigFieldFillStrategy($validator);
    }

    public function testCanHandleReturnsTrueForValidSection(): void
    {
        $data = [
            'skuBigFieldInfo' => [
                'description' => 'Product description',
            ],
        ];

        $this->assertTrue($this->strategy->canHandle($data, 'skuBigFieldInfo'));
    }

    public function testCanHandleReturnsFalseForInvalidSection(): void
    {
        $data = [
            'skuBigFieldInfo' => [
                'description' => 'Product description',
            ],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'skuBaseInfo'));
    }

    public function testCanHandleReturnsFalseForMissingSection(): void
    {
        $data = [
            'otherField' => ['value'],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'skuBigFieldInfo'));
    }

    public function testFillDescription(): void
    {
        $sku = new Sku();
        $data = [
            'skuBigFieldInfo' => [
                'description' => 'This is a product description',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBigFieldInfo');

        $bigFieldInfo = $sku->getBigFieldInfo();
        $this->assertSame('This is a product description', $bigFieldInfo->getDescription());
    }

    public function testFillIntroduction(): void
    {
        $sku = new Sku();
        $data = [
            'skuBigFieldInfo' => [
                'introduction' => '<p>Product introduction</p>',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBigFieldInfo');

        $bigFieldInfo = $sku->getBigFieldInfo();
        $this->assertSame('<p>Product introduction</p>', $bigFieldInfo->getIntroduction());
    }

    public function testFillAllFields(): void
    {
        $sku = new Sku();
        $data = [
            'skuBigFieldInfo' => [
                'description' => 'Product description',
                'introduction' => '<p>Introduction</p>',
                'wReadMe' => 'Read me content',
                'pcWdis' => 'PC warranty disclaimer',
                'pcHtmlContent' => '<div>HTML content</div>',
                'pcJsContent' => 'console.log("test");',
                'pcCssContent' => '.test { color: red; }',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBigFieldInfo');

        $bigFieldInfo = $sku->getBigFieldInfo();
        $this->assertSame('Product description', $bigFieldInfo->getDescription());
        $this->assertSame('<p>Introduction</p>', $bigFieldInfo->getIntroduction());
        $this->assertSame('Read me content', $bigFieldInfo->getWReadMe());
        $this->assertSame('PC warranty disclaimer', $bigFieldInfo->getPcWdis());
        $this->assertSame('<div>HTML content</div>', $bigFieldInfo->getPcHtmlContent());
        $this->assertSame('console.log("test");', $bigFieldInfo->getPcJsContent());
        $this->assertSame('.test { color: red; }', $bigFieldInfo->getPcCssContent());
    }

    public function testFillWithMissingFields(): void
    {
        $sku = new Sku();
        $data = [
            'skuBigFieldInfo' => [
                'description' => 'Only description',
            ],
        ];

        $this->strategy->fill($sku, $data, 'skuBigFieldInfo');

        $bigFieldInfo = $sku->getBigFieldInfo();
        $this->assertSame('Only description', $bigFieldInfo->getDescription());
        $this->assertNull($bigFieldInfo->getIntroduction());
        $this->assertNull($bigFieldInfo->getWReadMe());
    }

    public function testFillWithEmptyArray(): void
    {
        $sku = new Sku();
        $data = [
            'skuBigFieldInfo' => [],
        ];

        $this->strategy->fill($sku, $data, 'skuBigFieldInfo');

        $bigFieldInfo = $sku->getBigFieldInfo();
        $this->assertNull($bigFieldInfo->getDescription());
        $this->assertNull($bigFieldInfo->getIntroduction());
    }
}
