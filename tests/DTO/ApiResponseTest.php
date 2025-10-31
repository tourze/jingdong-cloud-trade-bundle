<?php

namespace JingdongCloudTradeBundle\Tests\DTO;

use JingdongCloudTradeBundle\DTO\ApiResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ApiResponse::class)]
final class ApiResponseTest extends TestCase
{
    public function testConstructor(): void
    {
        $skuList = ['sku1' => ['name' => 'Product 1']];
        $response = new ApiResponse(
            success: true,
            skuList: $skuList,
            total: 1,
            errorMsg: null
        );

        $this->assertTrue($response->success);
        $this->assertSame($skuList, $response->skuList);
        $this->assertSame(1, $response->total);
        $this->assertNull($response->errorMsg);
    }

    public function testFromArrayWithSuccessResponse(): void
    {
        $apiResult = [
            'result' => [
                'success' => true,
                'materialSkuVoList' => [
                    'sku1' => ['name' => 'Product 1'],
                    'sku2' => ['name' => 'Product 2'],
                ],
                'total' => 2,
            ],
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertTrue($response->success);
        $this->assertCount(2, $response->skuList);
        $this->assertSame(2, $response->total);
        $this->assertNull($response->errorMsg);
    }

    public function testFromArrayWithFailureResponse(): void
    {
        $apiResult = [
            'result' => [
                'success' => false,
                'errorMsg' => 'API request failed',
            ],
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertFalse($response->success);
        $this->assertSame([], $response->skuList);
        $this->assertSame(0, $response->total);
        $this->assertSame('API request failed', $response->errorMsg);
    }

    public function testFromArrayWithInvalidResultField(): void
    {
        $apiResult = [
            'result' => 'invalid',
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertFalse($response->success);
        $this->assertSame('响应格式错误：result字段不是数组', $response->errorMsg);
    }

    public function testFromArrayWithInvalidSkuListField(): void
    {
        $apiResult = [
            'result' => [
                'success' => true,
                'materialSkuVoList' => 'invalid',
                'total' => 0,
            ],
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertFalse($response->success);
        $this->assertSame('响应格式错误：materialSkuVoList字段不是数组', $response->errorMsg);
    }

    public function testFromArrayWithMissingSuccessField(): void
    {
        $apiResult = [
            'result' => [
                'materialSkuVoList' => [],
                'total' => 0,
            ],
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertFalse($response->success);
        $this->assertSame('未知错误', $response->errorMsg);
    }

    public function testFromArrayFiltersInvalidSkuListItems(): void
    {
        $apiResult = [
            'result' => [
                'success' => true,
                'materialSkuVoList' => [
                    'sku1' => ['name' => 'Product 1'],
                    123 => ['name' => 'Invalid Key'],  // Non-string key
                    'sku2' => 'invalid value',  // Non-array value
                    'sku3' => ['name' => 'Product 3'],
                ],
                'total' => 2,
            ],
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertTrue($response->success);
        $this->assertCount(2, $response->skuList);
        $this->assertArrayHasKey('sku1', $response->skuList);
        $this->assertArrayHasKey('sku3', $response->skuList);
        $this->assertArrayNotHasKey('sku2', $response->skuList);
    }

    public function testFromArrayWithInvalidTotalType(): void
    {
        $apiResult = [
            'result' => [
                'success' => true,
                'materialSkuVoList' => [],
                'total' => 'invalid',
            ],
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertTrue($response->success);
        $this->assertSame(0, $response->total);
    }

    public function testFromArrayWithNumericStringTotal(): void
    {
        $apiResult = [
            'result' => [
                'success' => true,
                'materialSkuVoList' => [],
                'total' => '42',
            ],
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertSame(42, $response->total);
    }

    public function testFromArrayWithFloatTotal(): void
    {
        $apiResult = [
            'result' => [
                'success' => true,
                'materialSkuVoList' => [],
                'total' => 42.7,
            ],
        ];

        $response = ApiResponse::fromArray($apiResult);

        $this->assertSame(42, $response->total);
    }

    public function testHasDataReturnsTrueWhenSuccessAndHasSkus(): void
    {
        $response = new ApiResponse(
            success: true,
            skuList: ['sku1' => ['name' => 'Product 1']],
            total: 1
        );

        $this->assertTrue($response->hasData());
    }

    public function testHasDataReturnsFalseWhenNotSuccess(): void
    {
        $response = new ApiResponse(
            success: false,
            skuList: ['sku1' => ['name' => 'Product 1']],
            total: 1,
            errorMsg: 'Error'
        );

        $this->assertFalse($response->hasData());
    }

    public function testHasDataReturnsFalseWhenEmptySkuList(): void
    {
        $response = new ApiResponse(
            success: true,
            skuList: [],
            total: 0
        );

        $this->assertFalse($response->hasData());
    }
}
