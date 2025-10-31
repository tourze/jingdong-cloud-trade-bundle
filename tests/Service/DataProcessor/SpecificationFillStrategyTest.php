<?php

namespace JingdongCloudTradeBundle\Tests\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Service\DataProcessor\ArrayDataValidator;
use JingdongCloudTradeBundle\Service\DataProcessor\SpecificationFillStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SpecificationFillStrategy::class)]
final class SpecificationFillStrategyTest extends TestCase
{
    private SpecificationFillStrategy $strategy;

    protected function setUp(): void
    {
        $validator = new ArrayDataValidator();
        $this->strategy = new SpecificationFillStrategy($validator);
    }

    public function testCanHandleReturnsTrueForValidSection(): void
    {
        $data = [
            'specifications' => [
                'spec1' => ['name' => 'Color', 'value' => 'Red'],
            ],
        ];

        $this->assertTrue($this->strategy->canHandle($data, 'specifications'));
    }

    public function testCanHandleReturnsFalseForInvalidSection(): void
    {
        $data = [
            'specifications' => [
                'spec1' => ['name' => 'Color'],
            ],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'imageInfos'));
    }

    public function testCanHandleReturnsFalseForMissingSection(): void
    {
        $data = [
            'otherField' => ['value'],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'specifications'));
    }

    public function testCanHandleReturnsFalseForNonArraySection(): void
    {
        $data = [
            'specifications' => 'not an array',
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'specifications'));
    }

    public function testFillWithSpecifications(): void
    {
        $sku = new Sku();
        $data = [
            'specifications' => [
                'spec1' => ['name' => 'Color', 'value' => 'Red'],
                'spec2' => ['name' => 'Size', 'value' => 'Large'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'specifications');

        $specification = $sku->getSpecification();
        $specs = $specification->getSpecifications();
        $this->assertIsArray($specs);
        $this->assertCount(2, $specs);
    }

    public function testFillWithExtAttributes(): void
    {
        $sku = new Sku();
        $data = [
            'specifications' => [],
            'extAtts' => [
                'attr1' => ['name' => 'Attribute 1', 'value' => 'Value 1'],
                'attr2' => ['name' => 'Attribute 2', 'value' => 'Value 2'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'specifications');

        $specification = $sku->getSpecification();
        $extAttrs = $specification->getExtAttributes();
        $this->assertIsArray($extAttrs);
        $this->assertCount(2, $extAttrs);
    }

    public function testFillWithBothSpecificationsAndExtAttributes(): void
    {
        $sku = new Sku();
        $data = [
            'specifications' => [
                'spec1' => ['name' => 'Color', 'value' => 'Red'],
            ],
            'extAtts' => [
                'attr1' => ['name' => 'Attribute 1', 'value' => 'Value 1'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'specifications');

        $specification = $sku->getSpecification();
        $specs = $specification->getSpecifications();
        $extAttrs = $specification->getExtAttributes();
        $this->assertNotNull($specs);
        $this->assertNotNull($extAttrs);
        $this->assertCount(1, $specs);
        $this->assertCount(1, $extAttrs);
    }

    public function testFillWithEmptySpecifications(): void
    {
        $sku = new Sku();
        $data = [
            'specifications' => [],
        ];

        $this->strategy->fill($sku, $data, 'specifications');

        $specification = $sku->getSpecification();
        $this->assertSame([], $specification->getSpecifications());
    }

    public function testFillWithMissingExtAttributes(): void
    {
        $sku = new Sku();
        $data = [
            'specifications' => [
                'spec1' => ['name' => 'Color', 'value' => 'Red'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'specifications');

        $specification = $sku->getSpecification();
        $this->assertSame([], $specification->getExtAttributes());
    }

    public function testFillWithNonArrayExtAttributes(): void
    {
        $sku = new Sku();
        $data = [
            'specifications' => [],
            'extAtts' => 'not an array',
        ];

        $this->strategy->fill($sku, $data, 'specifications');

        $specification = $sku->getSpecification();
        $this->assertSame([], $specification->getExtAttributes());
    }

    public function testFillConvertsToArrayValues(): void
    {
        $sku = new Sku();
        $data = [
            'specifications' => [
                'spec1' => ['name' => 'Color', 'value' => 'Red'],
                'spec2' => ['name' => 'Size', 'value' => 'Large'],
            ],
            'extAtts' => [
                'attr1' => ['name' => 'Attr1', 'value' => 'Val1'],
            ],
        ];

        $this->strategy->fill($sku, $data, 'specifications');

        $specification = $sku->getSpecification();
        $specs = $specification->getSpecifications();
        $extAttrs = $specification->getExtAttributes();

        $this->assertNotNull($specs);
        $this->assertNotNull($extAttrs);

        // Verify that specifications are converted to numeric-indexed array
        $this->assertArrayHasKey(0, $specs);
        $this->assertArrayHasKey(1, $specs);
        $this->assertArrayNotHasKey('spec1', $specs);

        // Verify that extAttributes are converted to numeric-indexed array
        $this->assertArrayHasKey(0, $extAttrs);
        $this->assertArrayNotHasKey('attr1', $extAttrs);
    }
}
