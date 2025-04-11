<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    private ?string $id = null;

    /**
     * ISBN
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => 'ISBN'])]
    private ?string $isbn = null;

    /**
     * ISSN
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => 'ISSN'])]
    private ?string $issn = null;

    /**
     * 条形码
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '条形码'])]
    private ?string $barCode = null;

    /**
     * 营销书名
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '营销书名'])]
    private ?string $bookName = null;

    /**
     * 外文书名
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '外文书名'])]
    private ?string $foreignBookName = null;

    /**
     * 作者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '作者'])]
    private ?string $author = null;

    /**
     * 译者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '译者'])]
    private ?string $transfer = null;

    /**
     * 编者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '编者'])]
    private ?string $editer = null;

    /**
     * 编纂者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '编纂者'])]
    private ?string $compile = null;

    /**
     * 绘者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '绘者'])]
    private ?string $drawer = null;

    /**
     * 摄影者
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '摄影者'])]
    private ?string $photography = null;

    /**
     * 校对
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '校对'])]
    private ?string $proofreader = null;

    /**
     * 出版社
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '出版社'])]
    private ?string $publishers = null;

    /**
     * 出版社号
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '出版社号'])]
    private ?string $publishNo = null;

    /**
     * 出版时间
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '出版时间'])]
    private ?string $publishTime = null;

    /**
     * 印刷时间
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '印刷时间'])]
    private ?string $printTime = null;

    /**
     * 版次
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '版次'])]
    private ?string $batchNo = null;

    /**
     * 印次
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '印次'])]
    private ?string $printNo = null;

    /**
     * 页数
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '页数'])]
    private ?string $pages = null;

    /**
     * 字数
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '字数'])]
    private ?string $letters = null;

    /**
     * 丛书名
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '丛书名'])]
    private ?string $series = null;

    /**
     * 图书语言
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '图书语言'])]
    private ?string $language = null;

    /**
     * 尺寸及重量
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '尺寸及重量'])]
    private ?string $sizeAndHeight = null;

    /**
     * 包装(装帧)
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '包装(装帧)'])]
    private ?string $packageStr = null;

    /**
     * 格式
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '格式'])]
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
    private ?string $brand = null;

    /**
     * 审图号
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '审图号'])]
    private ?string $picNo = null;

    /**
     * 中国法分类号
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '中国法分类号'])]
    private ?string $chinaCatalog = null;

    /**
     * 图书市场价
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '图书市场价'])]
    private ?string $marketPrice = null;

    /**
     * 注释信息
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '注释信息'])]
    private ?string $remarker = null;

    // Getters and Setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getIssn(): ?string
    {
        return $this->issn;
    }

    public function setIssn(?string $issn): self
    {
        $this->issn = $issn;
        return $this;
    }

    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    public function setBarCode(?string $barCode): self
    {
        $this->barCode = $barCode;
        return $this;
    }

    public function getBookName(): ?string
    {
        return $this->bookName;
    }

    public function setBookName(?string $bookName): self
    {
        $this->bookName = $bookName;
        return $this;
    }

    public function getForeignBookName(): ?string
    {
        return $this->foreignBookName;
    }

    public function setForeignBookName(?string $foreignBookName): self
    {
        $this->foreignBookName = $foreignBookName;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getTransfer(): ?string
    {
        return $this->transfer;
    }

    public function setTransfer(?string $transfer): self
    {
        $this->transfer = $transfer;
        return $this;
    }

    public function getEditer(): ?string
    {
        return $this->editer;
    }

    public function setEditer(?string $editer): self
    {
        $this->editer = $editer;
        return $this;
    }

    public function getCompile(): ?string
    {
        return $this->compile;
    }

    public function setCompile(?string $compile): self
    {
        $this->compile = $compile;
        return $this;
    }

    public function getDrawer(): ?string
    {
        return $this->drawer;
    }

    public function setDrawer(?string $drawer): self
    {
        $this->drawer = $drawer;
        return $this;
    }

    public function getPhotography(): ?string
    {
        return $this->photography;
    }

    public function setPhotography(?string $photography): self
    {
        $this->photography = $photography;
        return $this;
    }

    public function getProofreader(): ?string
    {
        return $this->proofreader;
    }

    public function setProofreader(?string $proofreader): self
    {
        $this->proofreader = $proofreader;
        return $this;
    }

    public function getPublishers(): ?string
    {
        return $this->publishers;
    }

    public function setPublishers(?string $publishers): self
    {
        $this->publishers = $publishers;
        return $this;
    }

    public function getPublishNo(): ?string
    {
        return $this->publishNo;
    }

    public function setPublishNo(?string $publishNo): self
    {
        $this->publishNo = $publishNo;
        return $this;
    }

    public function getPublishTime(): ?string
    {
        return $this->publishTime;
    }

    public function setPublishTime(?string $publishTime): self
    {
        $this->publishTime = $publishTime;
        return $this;
    }

    public function getPrintTime(): ?string
    {
        return $this->printTime;
    }

    public function setPrintTime(?string $printTime): self
    {
        $this->printTime = $printTime;
        return $this;
    }

    public function getBatchNo(): ?string
    {
        return $this->batchNo;
    }

    public function setBatchNo(?string $batchNo): self
    {
        $this->batchNo = $batchNo;
        return $this;
    }

    public function getPrintNo(): ?string
    {
        return $this->printNo;
    }

    public function setPrintNo(?string $printNo): self
    {
        $this->printNo = $printNo;
        return $this;
    }

    public function getPages(): ?string
    {
        return $this->pages;
    }

    public function setPages(?string $pages): self
    {
        $this->pages = $pages;
        return $this;
    }

    public function getLetters(): ?string
    {
        return $this->letters;
    }

    public function setLetters(?string $letters): self
    {
        $this->letters = $letters;
        return $this;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(?string $series): self
    {
        $this->series = $series;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getSizeAndHeight(): ?string
    {
        return $this->sizeAndHeight;
    }

    public function setSizeAndHeight(?string $sizeAndHeight): self
    {
        $this->sizeAndHeight = $sizeAndHeight;
        return $this;
    }

    public function getPackageStr(): ?string
    {
        return $this->packageStr;
    }

    public function setPackageStr(?string $packageStr): self
    {
        $this->packageStr = $packageStr;
        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getPackNum(): ?int
    {
        return $this->packNum;
    }

    public function setPackNum(?int $packNum): self
    {
        $this->packNum = $packNum;
        return $this;
    }

    public function getAttachment(): ?string
    {
        return $this->attachment;
    }

    public function setAttachment(?string $attachment): self
    {
        $this->attachment = $attachment;
        return $this;
    }

    public function getAttachmentNum(): ?int
    {
        return $this->attachmentNum;
    }

    public function setAttachmentNum(?int $attachmentNum): self
    {
        $this->attachmentNum = $attachmentNum;
        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getPicNo(): ?string
    {
        return $this->picNo;
    }

    public function setPicNo(?string $picNo): self
    {
        $this->picNo = $picNo;
        return $this;
    }

    public function getChinaCatalog(): ?string
    {
        return $this->chinaCatalog;
    }

    public function setChinaCatalog(?string $chinaCatalog): self
    {
        $this->chinaCatalog = $chinaCatalog;
        return $this;
    }

    public function getMarketPrice(): ?string
    {
        return $this->marketPrice;
    }

    public function setMarketPrice(?string $marketPrice): self
    {
        $this->marketPrice = $marketPrice;
        return $this;
    }

    public function getRemarker(): ?string
    {
        return $this->remarker;
    }

    public function setRemarker(?string $remarker): self
    {
        $this->remarker = $remarker;
        return $this;
    }

    /**
     * 转换为数组
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