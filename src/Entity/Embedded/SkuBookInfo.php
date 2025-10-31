<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 京东图书商品特有信息
 */
#[ORM\Embeddable]
class SkuBookInfo
{
    /**
     * 图书编号
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '图书编号'])]
    #[Assert\Length(max: 64)]
    private ?string $id = null;

    /**
     * ISBN
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => 'ISBN'])]
    #[Assert\Length(max: 64)]
    private ?string $isbn = null;

    /**
     * ISSN
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => 'ISSN'])]
    #[Assert\Length(max: 64)]
    private ?string $issn = null;

    /**
     * 条形码
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '条形码'])]
    #[Assert\Length(max: 64)]
    private ?string $barCode = null;

    /**
     * 营销书名
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '营销书名'])]
    #[Assert\Length(max: 255)]
    private ?string $bookName = null;

    /**
     * 外文书名
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '外文书名'])]
    #[Assert\Length(max: 255)]
    private ?string $foreignBookName = null;

    /**
     * 作者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '作者'])]
    #[Assert\Length(max: 255)]
    private ?string $author = null;

    /**
     * 译者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '译者'])]
    #[Assert\Length(max: 255)]
    private ?string $transfer = null;

    /**
     * 编者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '编者'])]
    #[Assert\Length(max: 255)]
    private ?string $editer = null;

    /**
     * 编纂者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '编纂者'])]
    #[Assert\Length(max: 255)]
    private ?string $compile = null;

    /**
     * 绘者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '绘者'])]
    #[Assert\Length(max: 255)]
    private ?string $drawer = null;

    /**
     * 摄影者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '摄影者'])]
    #[Assert\Length(max: 255)]
    private ?string $photography = null;

    /**
     * 校对
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '校对'])]
    #[Assert\Length(max: 255)]
    private ?string $proofreader = null;

    /**
     * 出版社
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '出版社'])]
    #[Assert\Length(max: 255)]
    private ?string $publishers = null;

    /**
     * 出版社号
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '出版社号'])]
    #[Assert\Length(max: 64)]
    private ?string $publishNo = null;

    /**
     * 出版时间
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '出版时间'])]
    #[Assert\Length(max: 64)]
    private ?string $publishTime = null;

    /**
     * 印刷时间
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '印刷时间'])]
    #[Assert\Length(max: 64)]
    private ?string $printTime = null;

    /**
     * 版次
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '版次'])]
    #[Assert\Length(max: 64)]
    private ?string $batchNo = null;

    /**
     * 印次
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '印次'])]
    #[Assert\Length(max: 64)]
    private ?string $printNo = null;

    /**
     * 页数
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '页数'])]
    #[Assert\Length(max: 64)]
    private ?string $pages = null;

    /**
     * 字数
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '字数'])]
    #[Assert\Length(max: 64)]
    private ?string $letters = null;

    /**
     * 丛书名
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '丛书名'])]
    #[Assert\Length(max: 255)]
    private ?string $series = null;

    /**
     * 图书语言
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '图书语言'])]
    #[Assert\Length(max: 64)]
    private ?string $language = null;

    /**
     * 尺寸及重量
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '尺寸及重量'])]
    #[Assert\Length(max: 255)]
    private ?string $sizeAndHeight = null;

    /**
     * 包装(装帧)
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '包装(装帧)'])]
    #[Assert\Length(max: 255)]
    private ?string $packageStr = null;

    /**
     * 格式
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '格式'])]
    #[Assert\Length(max: 64)]
    private ?string $format = null;

    /**
     * 包册数
     */
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '包册数'])]
    private ?int $packNum = null;

    /**
     * 附件
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '附件'])]
    #[Assert\Length(max: 255)]
    private ?string $attachment = null;

    /**
     * 附件数量
     */
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '附件数量'])]
    private ?int $attachmentNum = null;

    /**
     * 品牌
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '品牌'])]
    #[Assert\Length(max: 255)]
    private ?string $brand = null;

    /**
     * 审图号
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '审图号'])]
    #[Assert\Length(max: 64)]
    private ?string $picNo = null;

    /**
     * 中国法分类号
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '中国法分类号'])]
    #[Assert\Length(max: 64)]
    private ?string $chinaCatalog = null;

    /**
     * 图书市场价
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '图书市场价'])]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,2})?$/')]
    private ?string $marketPrice = null;

    /**
     * 注释信息
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '注释信息'])]
    #[Assert\Length(max: 65535)]
    private ?string $remarker = null;

    // Getters and Setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): void
    {
        $this->isbn = $isbn;
    }

    public function getIssn(): ?string
    {
        return $this->issn;
    }

    public function setIssn(?string $issn): void
    {
        $this->issn = $issn;
    }

    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    public function setBarCode(?string $barCode): void
    {
        $this->barCode = $barCode;
    }

    public function getBookName(): ?string
    {
        return $this->bookName;
    }

    public function setBookName(?string $bookName): void
    {
        $this->bookName = $bookName;
    }

    public function getForeignBookName(): ?string
    {
        return $this->foreignBookName;
    }

    public function setForeignBookName(?string $foreignBookName): void
    {
        $this->foreignBookName = $foreignBookName;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getTransfer(): ?string
    {
        return $this->transfer;
    }

    public function setTransfer(?string $transfer): void
    {
        $this->transfer = $transfer;
    }

    public function getEditer(): ?string
    {
        return $this->editer;
    }

    public function setEditer(?string $editer): void
    {
        $this->editer = $editer;
    }

    public function getCompile(): ?string
    {
        return $this->compile;
    }

    public function setCompile(?string $compile): void
    {
        $this->compile = $compile;
    }

    public function getDrawer(): ?string
    {
        return $this->drawer;
    }

    public function setDrawer(?string $drawer): void
    {
        $this->drawer = $drawer;
    }

    public function getPhotography(): ?string
    {
        return $this->photography;
    }

    public function setPhotography(?string $photography): void
    {
        $this->photography = $photography;
    }

    public function getProofreader(): ?string
    {
        return $this->proofreader;
    }

    public function setProofreader(?string $proofreader): void
    {
        $this->proofreader = $proofreader;
    }

    public function getPublishers(): ?string
    {
        return $this->publishers;
    }

    public function setPublishers(?string $publishers): void
    {
        $this->publishers = $publishers;
    }

    public function getPublishNo(): ?string
    {
        return $this->publishNo;
    }

    public function setPublishNo(?string $publishNo): void
    {
        $this->publishNo = $publishNo;
    }

    public function getPublishTime(): ?string
    {
        return $this->publishTime;
    }

    public function setPublishTime(?string $publishTime): void
    {
        $this->publishTime = $publishTime;
    }

    public function getPrintTime(): ?string
    {
        return $this->printTime;
    }

    public function setPrintTime(?string $printTime): void
    {
        $this->printTime = $printTime;
    }

    public function getBatchNo(): ?string
    {
        return $this->batchNo;
    }

    public function setBatchNo(?string $batchNo): void
    {
        $this->batchNo = $batchNo;
    }

    public function getPrintNo(): ?string
    {
        return $this->printNo;
    }

    public function setPrintNo(?string $printNo): void
    {
        $this->printNo = $printNo;
    }

    public function getPages(): ?string
    {
        return $this->pages;
    }

    public function setPages(?string $pages): void
    {
        $this->pages = $pages;
    }

    public function getLetters(): ?string
    {
        return $this->letters;
    }

    public function setLetters(?string $letters): void
    {
        $this->letters = $letters;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(?string $series): void
    {
        $this->series = $series;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function getSizeAndHeight(): ?string
    {
        return $this->sizeAndHeight;
    }

    public function setSizeAndHeight(?string $sizeAndHeight): void
    {
        $this->sizeAndHeight = $sizeAndHeight;
    }

    public function getPackageStr(): ?string
    {
        return $this->packageStr;
    }

    public function setPackageStr(?string $packageStr): void
    {
        $this->packageStr = $packageStr;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function getPackNum(): ?int
    {
        return $this->packNum;
    }

    public function setPackNum(?int $packNum): void
    {
        $this->packNum = $packNum;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAttachment(?string $attachment): void
    {
        $this->attachment = $attachment;
    }

    public function getAttachmentNum(): ?int
    {
        return $this->attachmentNum;
    }

    public function setAttachmentNum(?int $attachmentNum): void
    {
        $this->attachmentNum = $attachmentNum;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getPicNo(): ?string
    {
        return $this->picNo;
    }

    public function setPicNo(?string $picNo): void
    {
        $this->picNo = $picNo;
    }

    public function getChinaCatalog(): ?string
    {
        return $this->chinaCatalog;
    }

    public function setChinaCatalog(?string $chinaCatalog): void
    {
        $this->chinaCatalog = $chinaCatalog;
    }

    public function getMarketPrice(): ?string
    {
        return $this->marketPrice;
    }

    public function setMarketPrice(?string $marketPrice): void
    {
        $this->marketPrice = $marketPrice;
    }

    public function getRemarker(): ?string
    {
        return $this->remarker;
    }

    public function setRemarker(?string $remarker): void
    {
        $this->remarker = $remarker;
    }

    /**
     * 转换为数组
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'isbn' => $this->isbn,
            'issn' => $this->issn,
            'barCode' => $this->barCode,
            'bookName' => $this->bookName,
            'foreignBookName' => $this->foreignBookName,
            'author' => $this->author,
            'transfer' => $this->transfer,
            'editer' => $this->editer,
            'compile' => $this->compile,
            'drawer' => $this->drawer,
            'photography' => $this->photography,
            'proofreader' => $this->proofreader,
            'publishers' => $this->publishers,
            'publishNo' => $this->publishNo,
            'publishTime' => $this->publishTime,
            'printTime' => $this->printTime,
            'batchNo' => $this->batchNo,
            'printNo' => $this->printNo,
            'pages' => $this->pages,
            'letters' => $this->letters,
            'series' => $this->series,
            'language' => $this->language,
            'sizeAndHeight' => $this->sizeAndHeight,
            'packageStr' => $this->packageStr,
            'format' => $this->format,
            'packNum' => $this->packNum,
            'attachment' => $this->attachment,
            'attachmentNum' => $this->attachmentNum,
            'brand' => $this->brand,
            'picNo' => $this->picNo,
            'chinaCatalog' => $this->chinaCatalog,
            'marketPrice' => $this->marketPrice,
            'remarker' => $this->remarker,
        ];
    }
}
