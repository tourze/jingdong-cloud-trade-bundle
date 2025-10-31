<?php

namespace JingdongCloudTradeBundle\Tests\Service\DataProcessor;

use JingdongCloudTradeBundle\Service\DataProcessor\ArrayDataValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ArrayDataValidator::class)]
final class ArrayDataValidatorTest extends TestCase
{
    private ArrayDataValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ArrayDataValidator();
    }

    public function testValidateStringKeyedArrayFiltersNonStringKeys(): void
    {
        $data = [
            'key1' => 'value1',
            123 => 'value2',
            'key2' => 'value3',
            456 => 'value4',
        ];

        $result = $this->validator->validateStringKeyedArray($data);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('key1', $result);
        $this->assertArrayHasKey('key2', $result);
        $this->assertSame('value1', $result['key1']);
        $this->assertSame('value3', $result['key2']);
    }

    public function testValidateStringKeyedArrayWithEmptyArray(): void
    {
        $result = $this->validator->validateStringKeyedArray([]);

        $this->assertSame([], $result);
    }

    public function testValidateStringKeyedArrayWithAllStringKeys(): void
    {
        $data = [
            'name' => 'Product',
            'price' => 99.99,
            'category' => 'Electronics',
        ];

        $result = $this->validator->validateStringKeyedArray($data);

        $this->assertSame($data, $result);
    }

    public function testValidateIntKeyedArrayFiltersNonIntKeys(): void
    {
        $data = [
            0 => 'value1',
            'key1' => 'value2',
            1 => 'value3',
            'key2' => 'value4',
            2 => 'value5',
        ];

        $result = $this->validator->validateIntKeyedArray($data);

        $this->assertCount(3, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertArrayHasKey(2, $result);
        $this->assertSame('value1', $result[0]);
        $this->assertSame('value3', $result[1]);
        $this->assertSame('value5', $result[2]);
    }

    public function testValidateIntKeyedArrayWithEmptyArray(): void
    {
        $result = $this->validator->validateIntKeyedArray([]);

        $this->assertSame([], $result);
    }

    public function testValidateImageInfoArrayFiltersInvalidItems(): void
    {
        $imageInfos = [
            0 => ['path' => '/image1.jpg', 'isPrimary' => '1'],
            'invalid' => ['path' => '/image2.jpg'],  // String key
            1 => 'invalid value',  // Non-array value
            2 => ['path' => '/image3.jpg', 'isPrimary' => '0'],
        ];

        $result = $this->validator->validateImageInfoArray($imageInfos);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(2, $result);
        $this->assertSame('/image1.jpg', $result[0]['path']);
        $this->assertSame('/image3.jpg', $result[2]['path']);
    }

    public function testValidateImageInfoArrayWithValidData(): void
    {
        $imageInfos = [
            0 => ['path' => '/image1.jpg', 'isPrimary' => '1'],
            1 => ['path' => '/image2.jpg', 'isPrimary' => '0'],
        ];

        $result = $this->validator->validateImageInfoArray($imageInfos);

        $this->assertCount(2, $result);
        $this->assertSame($imageInfos, $result);
    }

    public function testValidateImageInfoArrayWithEmptyArray(): void
    {
        $result = $this->validator->validateImageInfoArray([]);

        $this->assertSame([], $result);
    }

    public function testIsValidArrayFieldReturnsTrueForValidArrayField(): void
    {
        $data = [
            'field1' => ['value1', 'value2'],
            'field2' => 'string',
        ];

        $this->assertTrue($this->validator->isValidArrayField($data, 'field1'));
    }

    public function testIsValidArrayFieldReturnsFalseForNonArrayField(): void
    {
        $data = [
            'field1' => 'string',
            'field2' => 123,
        ];

        $this->assertFalse($this->validator->isValidArrayField($data, 'field1'));
        $this->assertFalse($this->validator->isValidArrayField($data, 'field2'));
    }

    public function testIsValidArrayFieldReturnsFalseForMissingField(): void
    {
        $data = [
            'field1' => ['value1'],
        ];

        $this->assertFalse($this->validator->isValidArrayField($data, 'nonexistent'));
    }

    public function testGetStringFieldReturnsStringValue(): void
    {
        $data = [
            'name' => 'Product Name',
            'price' => 99.99,
        ];

        $this->assertSame('Product Name', $this->validator->getStringField($data, 'name'));
    }

    public function testGetStringFieldReturnsNullForNonStringValue(): void
    {
        $data = [
            'price' => 99.99,
            'count' => 5,
            'available' => true,
        ];

        $this->assertNull($this->validator->getStringField($data, 'price'));
        $this->assertNull($this->validator->getStringField($data, 'count'));
        $this->assertNull($this->validator->getStringField($data, 'available'));
    }

    public function testGetStringFieldReturnsNullForMissingField(): void
    {
        $data = [
            'name' => 'Product',
        ];

        $this->assertNull($this->validator->getStringField($data, 'nonexistent'));
    }

    public function testGetStringFieldReturnsNullForNullValue(): void
    {
        $data = [
            'name' => null,
        ];

        $this->assertNull($this->validator->getStringField($data, 'name'));
    }
}
