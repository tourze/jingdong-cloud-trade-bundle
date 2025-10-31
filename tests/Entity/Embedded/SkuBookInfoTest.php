<?php

namespace JingdongCloudTradeBundle\Tests\Entity\Embedded;

use JingdongCloudTradeBundle\Entity\Embedded\SkuBookInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SkuBookInfo::class)]
final class SkuBookInfoTest extends TestCase
{
    #[DataProvider('propertiesProvider')]
    public function testGettersAndSetters(string $property, mixed $value): void
    {
        $entity = new SkuBookInfo();

        switch ($property) {
            case 'id':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setId($value);
                $this->assertEquals($value, $entity->getId());
                break;
            case 'isbn':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setIsbn($value);
                $this->assertEquals($value, $entity->getIsbn());
                break;
            case 'issn':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setIssn($value);
                $this->assertEquals($value, $entity->getIssn());
                break;
            case 'barCode':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setBarCode($value);
                $this->assertEquals($value, $entity->getBarCode());
                break;
            case 'bookName':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setBookName($value);
                $this->assertEquals($value, $entity->getBookName());
                break;
            case 'foreignBookName':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setForeignBookName($value);
                $this->assertEquals($value, $entity->getForeignBookName());
                break;
            case 'author':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setAuthor($value);
                $this->assertEquals($value, $entity->getAuthor());
                break;
            case 'transfer':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setTransfer($value);
                $this->assertEquals($value, $entity->getTransfer());
                break;
            case 'editer':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setEditer($value);
                $this->assertEquals($value, $entity->getEditer());
                break;
            case 'compile':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setCompile($value);
                $this->assertEquals($value, $entity->getCompile());
                break;
            case 'drawer':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setDrawer($value);
                $this->assertEquals($value, $entity->getDrawer());
                break;
            case 'photography':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPhotography($value);
                $this->assertEquals($value, $entity->getPhotography());
                break;
            case 'proofreader':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setProofreader($value);
                $this->assertEquals($value, $entity->getProofreader());
                break;
            case 'publishers':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPublishers($value);
                $this->assertEquals($value, $entity->getPublishers());
                break;
            case 'publishNo':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPublishNo($value);
                $this->assertEquals($value, $entity->getPublishNo());
                break;
            case 'publishTime':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPublishTime($value);
                $this->assertEquals($value, $entity->getPublishTime());
                break;
            case 'printTime':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPrintTime($value);
                $this->assertEquals($value, $entity->getPrintTime());
                break;
            case 'batchNo':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setBatchNo($value);
                $this->assertEquals($value, $entity->getBatchNo());
                break;
            case 'printNo':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPrintNo($value);
                $this->assertEquals($value, $entity->getPrintNo());
                break;
            case 'pages':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPages($value);
                $this->assertEquals($value, $entity->getPages());
                break;
            case 'letters':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setLetters($value);
                $this->assertEquals($value, $entity->getLetters());
                break;
            case 'series':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setSeries($value);
                $this->assertEquals($value, $entity->getSeries());
                break;
            case 'language':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setLanguage($value);
                $this->assertEquals($value, $entity->getLanguage());
                break;
            case 'sizeAndHeight':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setSizeAndHeight($value);
                $this->assertEquals($value, $entity->getSizeAndHeight());
                break;
            case 'packageStr':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPackageStr($value);
                $this->assertEquals($value, $entity->getPackageStr());
                break;
            case 'format':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setFormat($value);
                $this->assertEquals($value, $entity->getFormat());
                break;
            case 'packNum':
                $this->assertTrue(\is_int($value) || null === $value);
                $entity->setPackNum($value);
                $this->assertEquals($value, $entity->getPackNum());
                break;
            case 'attachment':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setAttachment($value);
                $this->assertEquals($value, $entity->getAttachment());
                break;
            case 'attachmentNum':
                $this->assertTrue(\is_int($value) || null === $value);
                $entity->setAttachmentNum($value);
                $this->assertEquals($value, $entity->getAttachmentNum());
                break;
            case 'brand':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setBrand($value);
                $this->assertEquals($value, $entity->getBrand());
                break;
            case 'picNo':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setPicNo($value);
                $this->assertEquals($value, $entity->getPicNo());
                break;
            case 'chinaCatalog':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setChinaCatalog($value);
                $this->assertEquals($value, $entity->getChinaCatalog());
                break;
            case 'marketPrice':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setMarketPrice($value);
                $this->assertEquals($value, $entity->getMarketPrice());
                break;
            case 'remarker':
                $this->assertTrue(\is_string($value) || null === $value);
                $entity->setRemarker($value);
                $this->assertEquals($value, $entity->getRemarker());
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
        yield 'id' => ['id', 'BOOK123456'];
        yield 'isbn' => ['isbn', '978-7-111-12345-6'];
        yield 'issn' => ['issn', '1007-1234'];
        yield 'barCode' => ['barCode', '1234567890123'];
        yield 'bookName' => ['bookName', 'PHP编程实战'];
        yield 'foreignBookName' => ['foreignBookName', 'PHP Programming in Practice'];
        yield 'author' => ['author', '张三'];
        yield 'transfer' => ['transfer', '李四'];
        yield 'editer' => ['editer', '王五'];
        yield 'compile' => ['compile', '赵六'];
        yield 'drawer' => ['drawer', '钱七'];
        yield 'photography' => ['photography', '孙八'];
        yield 'proofreader' => ['proofreader', '周九'];
        yield 'publishers' => ['publishers', '机械工业出版社'];
        yield 'publishNo' => ['publishNo', '12345'];
        yield 'publishTime' => ['publishTime', '2023-01-01'];
        yield 'printTime' => ['printTime', '2023-02-01'];
        yield 'batchNo' => ['batchNo', '第1版'];
        yield 'printNo' => ['printNo', '第1次印刷'];
        yield 'pages' => ['pages', '320'];
        yield 'letters' => ['letters', '400千字'];
        yield 'series' => ['series', '计算机技术丛书'];
        yield 'language' => ['language', '中文'];
        yield 'sizeAndHeight' => ['sizeAndHeight', '185mm×260mm×20mm'];
        yield 'packageStr' => ['packageStr', '平装'];
        yield 'format' => ['format', '16开'];
        yield 'packNum' => ['packNum', 1];
        yield 'attachment' => ['attachment', '光盘'];
        yield 'attachmentNum' => ['attachmentNum', 1];
        yield 'brand' => ['brand', '机械工业出版社'];
        yield 'picNo' => ['picNo', '12345'];
        yield 'chinaCatalog' => ['chinaCatalog', 'TP312'];
        yield 'marketPrice' => ['marketPrice', '89.00'];
        yield 'remarker' => ['remarker', '这是一本优秀的PHP编程书籍'];
    }

    public function testToArray(): void
    {
        $entity = new SkuBookInfo();
        $entity->setId('BOOK123456');
        $entity->setIsbn('978-7-111-12345-6');
        $entity->setBookName('PHP编程实战');
        $entity->setAuthor('张三');
        $entity->setPublishers('机械工业出版社');

        $array = $entity->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('BOOK123456', $array['id']);
        $this->assertEquals('978-7-111-12345-6', $array['isbn']);
        $this->assertEquals('PHP编程实战', $array['bookName']);
        $this->assertEquals('张三', $array['author']);
        $this->assertEquals('机械工业出版社', $array['publishers']);
        $this->assertArrayHasKey('issn', $array);
        $this->assertArrayHasKey('barCode', $array);
        $this->assertArrayHasKey('foreignBookName', $array);
        $this->assertArrayHasKey('transfer', $array);
        $this->assertArrayHasKey('editer', $array);
        $this->assertArrayHasKey('compile', $array);
        $this->assertArrayHasKey('drawer', $array);
        $this->assertArrayHasKey('photography', $array);
        $this->assertArrayHasKey('proofreader', $array);
        $this->assertArrayHasKey('publishNo', $array);
        $this->assertArrayHasKey('publishTime', $array);
        $this->assertArrayHasKey('printTime', $array);
        $this->assertArrayHasKey('batchNo', $array);
        $this->assertArrayHasKey('printNo', $array);
        $this->assertArrayHasKey('pages', $array);
        $this->assertArrayHasKey('letters', $array);
        $this->assertArrayHasKey('series', $array);
        $this->assertArrayHasKey('language', $array);
        $this->assertArrayHasKey('sizeAndHeight', $array);
        $this->assertArrayHasKey('packageStr', $array);
        $this->assertArrayHasKey('format', $array);
        $this->assertArrayHasKey('packNum', $array);
        $this->assertArrayHasKey('attachment', $array);
        $this->assertArrayHasKey('attachmentNum', $array);
        $this->assertArrayHasKey('brand', $array);
        $this->assertArrayHasKey('picNo', $array);
        $this->assertArrayHasKey('chinaCatalog', $array);
        $this->assertArrayHasKey('marketPrice', $array);
        $this->assertArrayHasKey('remarker', $array);
    }
}
