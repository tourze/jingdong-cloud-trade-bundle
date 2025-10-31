<?php

namespace JingdongCloudTradeBundle\Tests\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuBigFieldInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SkuBigFieldInfo::class)]
final class SkuBigFieldInfoTest extends TestCase
{
    #[DataProvider('propertiesProvider')]
    public function testGettersAndSetters(string $property, mixed $value): void
    {
        $entity = new SkuBigFieldInfo();

        switch ($property) {
            case 'pcWdis':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPcWdis($value);
                $this->assertEquals($value, $entity->getPcWdis());
                break;
            case 'pcHtmlContent':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPcHtmlContent($value);
                $this->assertEquals($value, $entity->getPcHtmlContent());
                break;
            case 'pcJsContent':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPcJsContent($value);
                $this->assertEquals($value, $entity->getPcJsContent());
                break;
            case 'pcCssContent':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPcCssContent($value);
                $this->assertEquals($value, $entity->getPcCssContent());
                break;
            case 'description':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setDescription($value);
                $this->assertEquals($value, $entity->getDescription());
                break;
            case 'introduction':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setIntroduction($value);
                $this->assertEquals($value, $entity->getIntroduction());
                break;
            case 'wReadMe':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setWReadMe($value);
                $this->assertEquals($value, $entity->getWReadMe());
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
        yield 'pcWdis' => ['pcWdis', 'PC端商品介绍信息'];
        yield 'pcHtmlContent' => ['pcHtmlContent', '<html><body>PC HTML内容</body></html>'];
        yield 'pcJsContent' => ['pcJsContent', 'console.log("PC JS内容");'];
        yield 'pcCssContent' => ['pcCssContent', 'body { background: #fff; }'];
        yield 'description' => ['description', '商品详情信息'];
        yield 'introduction' => ['introduction', '商品介绍信息'];
        yield 'wReadMe' => ['wReadMe', '产品说明信息'];
    }

    public function testToArray(): void
    {
        $entity = new SkuBigFieldInfo();
        $entity->setPcWdis('PC端商品介绍信息');
        $entity->setPcHtmlContent('<html><body>PC HTML内容</body></html>');
        $entity->setDescription('商品详情信息');

        $array = $entity->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('PC端商品介绍信息', $array['pcWdis']);
        $this->assertEquals('<html><body>PC HTML内容</body></html>', $array['pcHtmlContent']);
        $this->assertEquals('商品详情信息', $array['description']);
        $this->assertArrayHasKey('pcJsContent', $array);
        $this->assertArrayHasKey('pcCssContent', $array);
        $this->assertArrayHasKey('introduction', $array);
        $this->assertArrayHasKey('wReadMe', $array);
    }
}
