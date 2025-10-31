<?php

namespace JingdongCloudTradeBundle\Tests\Service\DataProcessor;

use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Service\DataProcessor\ArrayDataValidator;
use JingdongCloudTradeBundle\Service\DataProcessor\BookInfoFillStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BookInfoFillStrategy::class)]
final class BookInfoFillStrategyTest extends TestCase
{
    private BookInfoFillStrategy $strategy;

    protected function setUp(): void
    {
        $validator = new ArrayDataValidator();
        $this->strategy = new BookInfoFillStrategy($validator);
    }

    public function testCanHandleReturnsTrueForValidSection(): void
    {
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'isbn' => '9787000000000',
                ],
            ],
        ];

        $this->assertTrue($this->strategy->canHandle($data, 'bookSkuBaseInfo'));
    }

    public function testCanHandleReturnsFalseForMissingSkuBaseInfo(): void
    {
        $data = [
            'otherField' => ['value'],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'bookSkuBaseInfo'));
    }

    public function testCanHandleReturnsFalseForMissingBookSkuBaseInfo(): void
    {
        $data = [
            'skuBaseInfo' => [
                'skuId' => '123456',
            ],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'bookSkuBaseInfo'));
    }

    public function testCanHandleReturnsFalseForInvalidSection(): void
    {
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [],
            ],
        ];

        $this->assertFalse($this->strategy->canHandle($data, 'imageInfos'));
    }

    public function testFillBookBasicInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'id' => 'BOOK-001',
                    'barCode' => '1234567890123',
                    'bookName' => 'Test Book',
                    'foreignBookName' => 'Test Book (Foreign)',
                    'ISBN' => '9787000000000',
                ],
            ],
        ];

        $this->strategy->fill($sku, $data, 'bookSkuBaseInfo');

        $bookInfo = $sku->getBookInfo();
        $this->assertSame('BOOK-001', $bookInfo->getId());
        $this->assertSame('1234567890123', $bookInfo->getBarCode());
        $this->assertSame('Test Book', $bookInfo->getBookName());
        $this->assertSame('Test Book (Foreign)', $bookInfo->getForeignBookName());
        $this->assertSame('9787000000000', $bookInfo->getIsbn());
    }

    public function testFillBookBasicInfoWithLowercaseIsbn(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'isbn' => '9787000000000',
                ],
            ],
        ];

        $this->strategy->fill($sku, $data, 'bookSkuBaseInfo');

        $bookInfo = $sku->getBookInfo();
        $this->assertSame('9787000000000', $bookInfo->getIsbn());
    }

    public function testFillBookBasicInfoWithIssn(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'ISSN' => '1234-5678',
                ],
            ],
        ];

        $this->strategy->fill($sku, $data, 'bookSkuBaseInfo');

        $bookInfo = $sku->getBookInfo();
        $this->assertSame('1234-5678', $bookInfo->getIssn());
    }

    public function testFillBookAuthorInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'author' => 'John Doe',
                    'transfer' => 'Jane Smith',
                    'editer' => 'Bob Editor',
                    'compile' => 'Alice Compiler',
                    'drawer' => 'Charlie Artist',
                    'photography' => 'David Photographer',
                    'proofreader' => 'Eve Proofreader',
                ],
            ],
        ];

        $this->strategy->fill($sku, $data, 'bookSkuBaseInfo');

        $bookInfo = $sku->getBookInfo();
        $this->assertSame('John Doe', $bookInfo->getAuthor());
        $this->assertSame('Jane Smith', $bookInfo->getTransfer());
        $this->assertSame('Bob Editor', $bookInfo->getEditer());
        $this->assertSame('Alice Compiler', $bookInfo->getCompile());
        $this->assertSame('Charlie Artist', $bookInfo->getDrawer());
        $this->assertSame('David Photographer', $bookInfo->getPhotography());
        $this->assertSame('Eve Proofreader', $bookInfo->getProofreader());
    }

    public function testFillBookPublishInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'publishers' => 'Test Publisher',
                    'publishNo' => 'PUB-001',
                    'publishTime' => '2023-01-01',
                    'printTime' => '2023-02-01',
                    'batchNo' => 'BATCH-001',
                    'printNo' => 'PRINT-001',
                ],
            ],
        ];

        $this->strategy->fill($sku, $data, 'bookSkuBaseInfo');

        $bookInfo = $sku->getBookInfo();
        $this->assertSame('Test Publisher', $bookInfo->getPublishers());
        $this->assertSame('PUB-001', $bookInfo->getPublishNo());
        $this->assertSame('2023-01-01', $bookInfo->getPublishTime());
        $this->assertSame('2023-02-01', $bookInfo->getPrintTime());
        $this->assertSame('BATCH-001', $bookInfo->getBatchNo());
        $this->assertSame('PRINT-001', $bookInfo->getPrintNo());
    }

    public function testFillBookPhysicalInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'pages' => '350',
                    'letters' => '250000',
                    'sizeAndHeight' => '210x297mm',
                    'packageStr' => 'Hardcover',
                    'format' => '16开',
                    'packNum' => 5,
                    'attachmentNum' => 2,
                ],
            ],
        ];

        $this->strategy->fill($sku, $data, 'bookSkuBaseInfo');

        $bookInfo = $sku->getBookInfo();
        $this->assertSame('350', $bookInfo->getPages());
        $this->assertSame('250000', $bookInfo->getLetters());
        $this->assertSame('210x297mm', $bookInfo->getSizeAndHeight());
        $this->assertSame('Hardcover', $bookInfo->getPackageStr());
        $this->assertSame('16开', $bookInfo->getFormat());
        $this->assertSame(5, $bookInfo->getPackNum());
        $this->assertSame(2, $bookInfo->getAttachmentNum());
    }

    public function testFillBookAdditionalInfo(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'series' => 'Test Series',
                    'language' => 'Chinese',
                    'attachment' => 'CD-ROM',
                    'brand' => 'Test Brand',
                    'picNo' => 'PIC-001',
                    'chinaCatalog' => 'CAT-001',
                    'marketPrice' => '99.99',
                    'remarker' => 'Special note',
                ],
            ],
        ];

        $this->strategy->fill($sku, $data, 'bookSkuBaseInfo');

        $bookInfo = $sku->getBookInfo();
        $this->assertSame('Test Series', $bookInfo->getSeries());
        $this->assertSame('Chinese', $bookInfo->getLanguage());
        $this->assertSame('CD-ROM', $bookInfo->getAttachment());
        $this->assertSame('Test Brand', $bookInfo->getBrand());
        $this->assertSame('PIC-001', $bookInfo->getPicNo());
        $this->assertSame('CAT-001', $bookInfo->getChinaCatalog());
        $this->assertSame('99.99', $bookInfo->getMarketPrice());
        $this->assertSame('Special note', $bookInfo->getRemarker());
    }

    public function testFillWithMissingFields(): void
    {
        $sku = new Sku();
        $data = [
            'skuBaseInfo' => [
                'bookSkuBaseInfo' => [
                    'isbn' => '9787000000000',
                ],
            ],
        ];

        $this->strategy->fill($sku, $data, 'bookSkuBaseInfo');

        $bookInfo = $sku->getBookInfo();
        $this->assertSame('9787000000000', $bookInfo->getIsbn());
        $this->assertNull($bookInfo->getAuthor());
        $this->assertNull($bookInfo->getPublishers());
    }
}
