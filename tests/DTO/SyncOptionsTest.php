<?php

namespace JingdongCloudTradeBundle\Tests\DTO;

use JingdongCloudTradeBundle\DTO\SyncOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SyncOptions::class)]
final class SyncOptionsTest extends TestCase
{
    public function testConstructorWithDefaults(): void
    {
        $options = new SyncOptions();

        $this->assertSame(1000, $options->limit);
        $this->assertFalse($options->force);
        $this->assertNull($options->categoryId);
        $this->assertNull($options->brandId);
    }

    public function testConstructorWithCustomValues(): void
    {
        $options = new SyncOptions(
            limit: 500,
            force: true,
            categoryId: '12345',
            brandId: '67890'
        );

        $this->assertSame(500, $options->limit);
        $this->assertTrue($options->force);
        $this->assertSame('12345', $options->categoryId);
        $this->assertSame('67890', $options->brandId);
    }

    public function testFromArrayWithEmptyArray(): void
    {
        $options = SyncOptions::fromArray([]);

        $this->assertSame(1000, $options->limit);
        $this->assertFalse($options->force);
        $this->assertNull($options->categoryId);
        $this->assertNull($options->brandId);
    }

    public function testFromArrayWithAllOptions(): void
    {
        $array = [
            'limit' => 200,
            'force' => true,
            'categoryId' => '99999',
            'brandId' => '88888',
        ];

        $options = SyncOptions::fromArray($array);

        $this->assertSame(200, $options->limit);
        $this->assertTrue($options->force);
        $this->assertSame('99999', $options->categoryId);
        $this->assertSame('88888', $options->brandId);
    }

    public function testFromArrayWithPartialOptions(): void
    {
        $array = [
            'limit' => 300,
            'categoryId' => '11111',
        ];

        $options = SyncOptions::fromArray($array);

        $this->assertSame(300, $options->limit);
        $this->assertFalse($options->force);
        $this->assertSame('11111', $options->categoryId);
        $this->assertNull($options->brandId);
    }

    public function testFromArrayTypeCasting(): void
    {
        $array = [
            'limit' => '500',
            'force' => 1,
            'categoryId' => 12345,
            'brandId' => 67890,
        ];

        $options = SyncOptions::fromArray($array);

        $this->assertSame(500, $options->limit);
        $this->assertTrue($options->force);
        $this->assertSame('12345', $options->categoryId);
        $this->assertSame('67890', $options->brandId);
    }

    public function testBuildRequestParamsBasic(): void
    {
        $options = new SyncOptions();
        $params = $options->buildRequestParams(1, 50);

        $this->assertSame([
            'page' => 1,
            'pageSize' => 50,
        ], $params);
    }

    public function testBuildRequestParamsWithCategoryId(): void
    {
        $options = new SyncOptions(categoryId: '12345');
        $params = $options->buildRequestParams(2, 100);

        $this->assertSame([
            'page' => 2,
            'pageSize' => 100,
            'cid3' => '12345',
        ], $params);
    }

    public function testBuildRequestParamsWithBrandId(): void
    {
        $options = new SyncOptions(brandId: '67890');
        $params = $options->buildRequestParams(3, 25);

        $this->assertSame([
            'page' => 3,
            'pageSize' => 25,
            'brandId' => '67890',
        ], $params);
    }

    public function testBuildRequestParamsWithAllFilters(): void
    {
        $options = new SyncOptions(
            categoryId: '12345',
            brandId: '67890'
        );
        $params = $options->buildRequestParams(1, 50);

        $this->assertSame([
            'page' => 1,
            'pageSize' => 50,
            'cid3' => '12345',
            'brandId' => '67890',
        ], $params);
    }
}
