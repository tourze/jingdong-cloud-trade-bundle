<?php

namespace JingdongCloudTradeBundle\Tests\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuSpecification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SkuSpecification::class)]
final class SkuSpecificationTest extends TestCase
{
    #[DataProvider('propertiesProvider')]
    public function testGettersAndSetters(string $property, mixed $value): void
    {
        $entity = new SkuSpecification();

        switch ($property) {
            case 'specifications':
                $this->assertTrue(\is_array($value) || null === $value);
                /** @var array<int, array<string, mixed>>|null $value */
                $entity->setSpecifications($value);
                $this->assertEquals($value, $entity->getSpecifications());
                break;
            case 'extAttributes':
                $this->assertTrue(\is_array($value) || null === $value);
                /** @var array<int, array<string, mixed>>|null $value */
                $entity->setExtAttributes($value);
                $this->assertEquals($value, $entity->getExtAttributes());
                break;
            case 'parameters':
                $this->assertTrue(\is_array($value) || null === $value);
                /** @var array<int, array<string, mixed>>|null $value */
                $entity->setParameters($value);
                $this->assertEquals($value, $entity->getParameters());
                break;
            case 'afterSalesInfo':
                $this->assertTrue(\is_array($value) || null === $value);
                /** @var array<int, array<string, mixed>>|null $value */
                $entity->setAfterSalesInfo($value);
                $this->assertEquals($value, $entity->getAfterSalesInfo());
                break;
            case 'score':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setScore($value);
                $this->assertEquals($value, $entity->getScore());
                break;
            case 'commentCount':
                $this->assertTrue(\is_int($value) || null === $value);
                $entity->setCommentCount($value);
                $this->assertEquals($value, $entity->getCommentCount());
                break;
            case 'promotionInfo':
                $this->assertTrue(\is_array($value) || null === $value);
                /** @var array<int, array<string, mixed>>|null $value */
                $entity->setPromotionInfo($value);
                $this->assertEquals($value, $entity->getPromotionInfo());
                break;
            case 'promotionLabel':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPromotionLabel($value);
                $this->assertEquals($value, $entity->getPromotionLabel());
                break;
            case 'promoPrice':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPromoPrice($value);
                $this->assertEquals($value, $entity->getPromoPrice());
                break;
            case 'hasPromotion':
                $this->assertIsBool($value);
                $entity->setHasPromotion($value);
                $this->assertEquals($value, $entity->hasPromotion());
                break;
            case 'priceUpdateTime':
                $this->assertTrue($value instanceof \DateTimeInterface || null === $value);
                $entity->setPriceUpdateTime($value);
                $this->assertEquals($value, $entity->getPriceUpdateTime());
                break;
            case 'stockUpdateTime':
                $this->assertTrue($value instanceof \DateTimeInterface || null === $value);
                $entity->setStockUpdateTime($value);
                $this->assertEquals($value, $entity->getStockUpdateTime());
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
        yield 'specifications' => ['specifications', /** @var array<int, array<string, mixed>> */ [
            [
                'groupName' => '颜色',
                'attributes' => [
                    [
                        'attName' => '颜色',
                        'valNames' => ['红色', '蓝色'],
                    ],
                ],
            ],
        ]];
        yield 'extAttributes' => ['extAttributes', /** @var array<int, array<string, mixed>> */ [
            [
                'attName' => '材质',
                'valNames' => ['纯棉', '涤纶'],
            ],
        ]];
        yield 'parameters' => ['parameters', /** @var array<int, array<string, mixed>> */ [
            '材质' => '纯棉',
            '尺码' => 'L',
            '重量' => '200g',
        ]];
        yield 'afterSalesInfo' => ['afterSalesInfo', /** @var array<int, array<string, mixed>> */ [
            '退换货' => '7天无理由退换',
            '保修' => '1年质保',
        ]];
        yield 'score' => ['score', '4.8'];
        yield 'commentCount' => ['commentCount', 1250];
        yield 'promotionInfo' => ['promotionInfo', /** @var array<int, array<string, mixed>> */ [
            'type' => '限时优惠',
            'discount' => '8折',
        ]];
        yield 'promotionLabel' => ['promotionLabel', '限时特惠'];
        yield 'promoPrice' => ['promoPrice', '199.00'];
        yield 'hasPromotion' => ['hasPromotion', true];
        yield 'priceUpdateTime' => ['priceUpdateTime', new \DateTimeImmutable()];
        yield 'stockUpdateTime' => ['stockUpdateTime', new \DateTimeImmutable()];
    }

    public function testToArray(): void
    {
        $now = new \DateTimeImmutable('2023-01-01 12:00:00');
        $entity = new SkuSpecification();
        $entity->setSpecifications([
            [
                'groupName' => '颜色',
                'attributes' => [
                    [
                        'attName' => '颜色',
                        'valNames' => ['红色', '蓝色'],
                    ],
                ],
            ],
        ]);
        $entity->setScore('4.8');
        $entity->setCommentCount(1250);
        $entity->setHasPromotion(true);
        $entity->setPromotionLabel('限时特惠');
        $entity->setPromoPrice('199.00');
        $entity->setPriceUpdateTime($now);
        $entity->setStockUpdateTime($now);

        $array = $entity->toArray();

        $this->assertIsArray($array);
        $this->assertIsArray($array['specifications']);
        $specifications = $array['specifications'];
        $this->assertIsArray($specifications);
        $firstSpec = $specifications[0];
        $this->assertIsArray($firstSpec);
        $this->assertEquals('颜色', $firstSpec['groupName']);
        $this->assertEquals('4.8', $array['score']);
        $this->assertEquals(1250, $array['commentCount']);
        $this->assertTrue($array['hasPromotion']);
        $this->assertEquals('限时特惠', $array['promotionLabel']);
        $this->assertEquals('199.00', $array['promoPrice']);
        $this->assertEquals('2023-01-01 12:00:00', $array['priceUpdateTime']);
        $this->assertEquals('2023-01-01 12:00:00', $array['stockUpdateTime']);
        $this->assertArrayHasKey('extAttributes', $array);
        $this->assertArrayHasKey('parameters', $array);
        $this->assertArrayHasKey('afterSalesInfo', $array);
        $this->assertArrayHasKey('promotionInfo', $array);
    }
}
