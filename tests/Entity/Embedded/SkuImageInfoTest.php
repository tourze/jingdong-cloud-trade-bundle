<?php

namespace JingdongCloudTradeBundle\Tests\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuImageInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SkuImageInfo::class)]
final class SkuImageInfoTest extends TestCase
{
    #[DataProvider('propertiesProvider')]
    public function testGettersAndSetters(string $property, mixed $value): void
    {
        $entity = new SkuImageInfo();

        switch ($property) {
            case 'imageUrl':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setImageUrl($value);
                $this->assertEquals($value, $entity->getImageUrl());
                break;
            case 'detailImages':
                $this->assertTrue(\is_array($value) || null === $value);
                /** @var array<string>|null $value */
                $entity->setDetailImages($value);
                $this->assertEquals($value, $entity->getDetailImages());
                break;
            case 'imageInfos':
                $this->assertTrue(\is_array($value) || null === $value);
                /** @var array<int, array<string, mixed>>|null $value */
                $entity->setImageInfos($value);
                $this->assertEquals($value, $entity->getImageInfos());
                break;
            default:
                self::markTestSkipped("Property {$property} is not supported");
        }
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'imageUrl' => ['imageUrl', 'https://example.com/image.jpg'];
        yield 'detailImages' => ['detailImages', /** @var array<string> */ [
            'https://example.com/detail1.jpg',
            'https://example.com/detail2.jpg',
            'https://example.com/detail3.jpg',
        ]];
        yield 'imageInfos' => ['imageInfos', /** @var array<int, array<string, mixed>> */ [
            [
                'path' => 'https://example.com/path1.jpg',
                'features' => '主图',
                'orderSort' => '1',
                'isPrimary' => '1',
                'position' => '1',
                'type' => '1',
            ],
            [
                'path' => 'https://example.com/path2.jpg',
                'features' => '细节图',
                'orderSort' => '2',
                'isPrimary' => '0',
                'position' => '2',
                'type' => '2',
            ],
        ]];
    }

    public function testToArray(): void
    {
        $entity = new SkuImageInfo();
        $entity->setImageUrl('https://example.com/image.jpg');
        $entity->setDetailImages([
            'https://example.com/detail1.jpg',
            'https://example.com/detail2.jpg',
        ]);
        $entity->setImageInfos([
            [
                'path' => 'https://example.com/path1.jpg',
                'features' => '主图',
                'orderSort' => '1',
                'isPrimary' => '1',
                'position' => '1',
                'type' => '1',
            ],
        ]);

        $array = $entity->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('https://example.com/image.jpg', $array['imageUrl']);
        $this->assertIsArray($array['detailImages']);
        $this->assertCount(2, $array['detailImages']);
        $this->assertEquals('https://example.com/detail1.jpg', $array['detailImages'][0]);
        $this->assertIsArray($array['imageInfos']);
        $this->assertCount(1, $array['imageInfos']);
        $imageInfos = $array['imageInfos'];
        $this->assertIsArray($imageInfos);
        $firstImageInfo = $imageInfos[0];
        $this->assertIsArray($firstImageInfo);
        $this->assertEquals('主图', $firstImageInfo['features']);
    }
}
