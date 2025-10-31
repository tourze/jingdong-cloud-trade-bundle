<?php

namespace JingdongCloudTradeBundle\Tests\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Service\DataProcessor\ArrayDataValidator;
use JingdongCloudTradeBundle\Service\DataProcessor\ImageInfoFillStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ImageInfoFillStrategy::class)]
final class ImageInfoFillStrategyTest extends TestCase
{
    private ImageInfoFillStrategy $strategy;

    protected function setUp(): void
    {
        $validator = new ArrayDataValidator();
        $this->strategy = new ImageInfoFillStrategy($validator);
    }

    public function testCanHandleReturnsTrueForValidSection(): void
    {
        $data = [
            'imageInfos' => [
                ['path' => '/image1.jpg'],
            ],
        ];

        $this->assertTrue($this->strategy->canHandle($data, 'imageInfos'));
    }

    public function testCanHandleReturnsFalseForInvalidSection(): void
    {
        $data = [
            'imageInfos' => [
                ['path' => '/image1.jpg'],
            ],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'skuBaseInfo'));
    }

    public function testCanHandleReturnsFalseForMissingSection(): void
    {
        $data = [
            'otherField' => ['value'],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'imageInfos'));
    }

    public function testCanHandleReturnsFalseForNonArraySection(): void
    {
        $data = [
            'imageInfos' => 'not an array',
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'imageInfos'));
    }

    public function testFillWithPrimaryImage(): void
    {
        $sku = new Sku();
        $data = [
            'imageInfos' => [
                0 => ['path' => '/image1.jpg', 'isPrimary' => '0'],
                1 => ['path' => '/image2.jpg', 'isPrimary' => '1'],
                2 => ['path' => '/image3.jpg', 'isPrimary' => '0'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'imageInfos');

        $imageInfo = $sku->getImageInfo();
        $this->assertSame('/image2.jpg', $imageInfo->getImageUrl());
        $imageInfos = $imageInfo->getImageInfos();
        $this->assertNotNull($imageInfos);
        $this->assertCount(3, $imageInfos);
    }

    public function testFillWithoutPrimaryImageUsesImgUrl(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'imgUrl' => '/fallback-image.jpg',
            ],
            'imageInfos' => [
                0 => ['path' => '/image1.jpg', 'isPrimary' => '0'],
                1 => ['path' => '/image2.jpg', 'isPrimary' => '0'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'imageInfos');

        $imageInfo = $sku->getImageInfo();
        $this->assertSame('/fallback-image.jpg', $imageInfo->getImageUrl());
    }

    public function testFillWithoutPrimaryImageAndNoImgUrl(): void
    {
        $sku = new Sku();
        $data = [
            'imageInfos' => [
                0 => ['path' => '/image1.jpg', 'isPrimary' => '0'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'imageInfos');

        $imageInfo = $sku->getImageInfo();
        $this->assertNull($imageInfo->getImageUrl());
    }

    public function testFillWithEmptyImageInfos(): void
    {
        $sku = new Sku();
        $data = [
            'imageInfos' => [],
        ];

        $this->strategy->fill($sku, $data, 'imageInfos');

        $imageInfo = $sku->getImageInfo();
        $this->assertSame([], $imageInfo->getImageInfos());
        $this->assertNull($imageInfo->getImageUrl());
    }

    public function testFillFiltersInvalidImageInfos(): void
    {
        $sku = new Sku();
        $data = [
            'imageInfos' => [
                0 => ['path' => '/image1.jpg'],
                'invalid' => ['path' => '/image2.jpg'],  // String key
                1 => 'not an array',  // Non-array value
                2 => ['path' => '/image3.jpg'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'imageInfos');

        $imageInfo = $sku->getImageInfo();
        $imageInfos = $imageInfo->getImageInfos();
        $this->assertNotNull($imageInfos);
        $this->assertCount(2, $imageInfos);
        $this->assertArrayHasKey(0, $imageInfos);
        $this->assertArrayHasKey(2, $imageInfos);
    }

    public function testFillPrefersPrimaryImageOverImgUrl(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'imgUrl' => '/fallback-image.jpg',
            ],
            'imageInfos' => [
                0 => ['path' => '/image1.jpg', 'isPrimary' => '1'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'imageInfos');

        $imageInfo = $sku->getImageInfo();
        $this->assertSame('/image1.jpg', $imageInfo->getImageUrl());
    }

    public function testFillWithNonArraySkuBaseInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => 'not an array',
            'imageInfos' => [
                0 => ['path' => '/image1.jpg'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'imageInfos');

        $imageInfo = $sku->getImageInfo();
        $this->assertNull($imageInfo->getImageUrl());
    }
}
