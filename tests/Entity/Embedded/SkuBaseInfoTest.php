<?php

namespace JingdongCloudTradeBundle\Tests\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuBaseInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SkuBaseInfo::class)]
final class SkuBaseInfoTest extends TestCase
{
    #[DataProvider('propertiesProvider')]
    public function testGettersAndSetters(string $property, mixed $value): void
    {
        $skuBaseInfo = new SkuBaseInfo();
        $skuBaseInfo->setSkuId('SKU123456');
        $skuBaseInfo->setSkuName('测试商品');
        $skuBaseInfo->setPrice('99.99');

        switch ($property) {
            case 'skuId':
                $this->assertIsString($value);
                $skuBaseInfo->setSkuId($value);
                $this->assertEquals($value, $skuBaseInfo->getSkuId());
                break;
            case 'skuName':
                $this->assertIsString($value);
                $skuBaseInfo->setSkuName($value);
                $this->assertEquals($value, $skuBaseInfo->getSkuName());
                break;
            case 'price':
                $this->assertIsString($value);
                $skuBaseInfo->setPrice($value);
                $this->assertEquals($value, $skuBaseInfo->getPrice());
                break;
            case 'marketPrice':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setMarketPrice($value);
                $this->assertEquals($value, $skuBaseInfo->getMarketPrice());
                break;
            case 'categoryId':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setCategoryId($value);
                $this->assertEquals($value, $skuBaseInfo->getCategoryId());
                break;
            case 'categoryName':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setCategoryName($value);
                $this->assertEquals($value, $skuBaseInfo->getCategoryName());
                break;
            case 'categoryId1':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setCategoryId1($value);
                $this->assertEquals($value, $skuBaseInfo->getCategoryId1());
                break;
            case 'categoryName1':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setCategoryName1($value);
                $this->assertEquals($value, $skuBaseInfo->getCategoryName1());
                break;
            case 'categoryId2':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setCategoryId2($value);
                $this->assertEquals($value, $skuBaseInfo->getCategoryId2());
                break;
            case 'categoryName2':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setCategoryName2($value);
                $this->assertEquals($value, $skuBaseInfo->getCategoryName2());
                break;
            case 'brandId':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setBrandId($value);
                $this->assertEquals($value, $skuBaseInfo->getBrandId());
                break;
            case 'brandName':
                $this->assertTrue(\is_string($value) || null === $value);
                $skuBaseInfo->setBrandName($value);
                $this->assertEquals($value, $skuBaseInfo->getBrandName());
                break;
            case 'weight':
                $this->assertTrue(\is_int($value) || null === $value);
                $skuBaseInfo->setWeight($value);
                $this->assertEquals($value, $skuBaseInfo->getWeight());
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
        yield 'skuId' => ['skuId', 'SKU987654'];
        yield 'skuName' => ['skuName', '另一个测试商品'];
        yield 'price' => ['price', '199.99'];
        yield 'marketPrice' => ['marketPrice', '299.99'];
        yield 'categoryId' => ['categoryId', 'CAT123456'];
        yield 'categoryName' => ['categoryName', '测试分类'];
        yield 'categoryId1' => ['categoryId1', 'CAT123456'];
        yield 'categoryName1' => ['categoryName1', '一级分类'];
        yield 'categoryId2' => ['categoryId2', 'CAT789012'];
        yield 'categoryName2' => ['categoryName2', '二级分类'];
        yield 'brandId' => ['brandId', 'BRAND123456'];
        yield 'brandName' => ['brandName', '测试品牌'];
        yield 'weight' => ['weight', 500];
    }

    public function testToArray(): void
    {
        $skuBaseInfo = new SkuBaseInfo();
        $skuBaseInfo->setSkuId('SKU123456');
        $skuBaseInfo->setSkuName('测试商品');
        $skuBaseInfo->setPrice('99.99');

        $array = $skuBaseInfo->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('SKU123456', $array['skuId']);
        $this->assertEquals('测试商品', $array['skuName']);
        $this->assertEquals('99.99', $array['price']);
    }
}
