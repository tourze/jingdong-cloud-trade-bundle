<?php

namespace JingdongCloudTradeBundle\Entity\Embedded;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCloudTradeBundle\Enum\DeliveryTypeEnum;
use JingdongCloudTradeBundle\Enum\FlashSaleEnum;
use JingdongCloudTradeBundle\Enum\OwnerTypeEnum;
use JingdongCloudTradeBundle\Enum\ReturnPolicyEnum;
use JingdongCloudTradeBundle\Enum\SkuStateEnum;
use JingdongCloudTradeBundle\Enum\StockStateEnum;

/**
 * 京东商品基础信息
 */
#[ORM\Embeddable]
class SkuBaseInfo
{
    /**
     * 商品SKU ID
     */
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '商品SKU ID'])]
    private string $skuId;

    /**
     * 商品名称
     */
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '商品名称'])]
    private string $skuName;

    /**
     * 商品价格
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '商品价格'])]
    private string $price;

    /**
     * 商品市场价格
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '商品市场价格'])]
    private ?string $marketPrice = null;

    /**
     * 商品分类ID
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '商品分类ID'])]
    private ?string $categoryId = null;

    /**
     * 商品分类名称
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '商品分类名称'])]
    private ?string $categoryName = null;

    /**
     * 一级分类ID
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '一级分类ID'])]
    private ?string $categoryId1 = null;

    /**
     * 一级分类名称
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '一级分类名称'])]
    private ?string $categoryName1 = null;

    /**
     * 二级分类ID
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '二级分类ID'])]
    private ?string $categoryId2 = null;

    /**
     * 二级分类名称
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '二级分类名称'])]
    private ?string $categoryName2 = null;

    /**
     * 商品品牌ID
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '商品品牌ID'])]
    private ?string $brandId = null;

    /**
     * 商品品牌名称
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '商品品牌名称'])]
    private ?string $brandName = null;

    /**
     * 商品状态
     */
    #[ORM\Column(type: Types::STRING, enumType: SkuStateEnum::class, options: ['comment' => '商品状态'])]
    private SkuStateEnum $state = SkuStateEnum::OFF_SALE;

    /**
     * 商品重量(g)
     */
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '商品重量(g)'])]
    private ?int $weight = null;

    /**
     * 商品销售属性
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '商品销售属性'])]
    private ?array $saleAttrs = null;

    /**
     * 商品库存
     */
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '商品库存', 'default' => 0])]
    private int $stock = 0;

    /**
     * 商品库存状态
     */
    #[ORM\Column(type: Types::STRING, enumType: StockStateEnum::class, options: ['comment' => '商品库存状态'])]
    private StockStateEnum $stockState = StockStateEnum::OUT_OF_STOCK;

    /**
     * 店铺类型
     */
    #[ORM\Column(type: Types::STRING, nullable: true, enumType: OwnerTypeEnum::class, options: ['comment' => '店铺类型'])]
    private ?OwnerTypeEnum $ownerType = null;

    /**
     * 店铺名称
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '店铺名称'])]
    private ?string $shopName = null;

    /**
     * 商家名称
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '商家名称'])]
    private ?string $vendorName = null;

    /**
     * 配送方式
     */
    #[ORM\Column(type: Types::STRING, enumType: DeliveryTypeEnum::class, nullable: true, options: ['comment' => '配送方式'])]
    private ?DeliveryTypeEnum $deliveryType = null;

    /**
     * 发货地址
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '发货地址'])]
    private ?string $delivery = null;

    /**
     * 配送区域
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '配送区域'])]
    private ?array $deliveryAreas = null;

    /**
     * 商品型号
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '商品型号'])]
    private ?string $model = null;

    /**
     * 商品规格
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '商品规格'])]
    private ?array $specs = null;

    /**
     * 颜色
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '颜色'])]
    private ?string $color = null;

    /**
     * 颜色顺序
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '颜色顺序'])]
    private ?string $colorSequence = null;

    /**
     * 尺码
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '尺码'])]
    private ?string $size = null;

    /**
     * 尺码顺序
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '尺码顺序'])]
    private ?string $sizeSequence = null;

    /**
     * 7天无理由退货
     */
    #[ORM\Column(type: Types::INTEGER, enumType: ReturnPolicyEnum::class, nullable: true, options: ['comment' => '7天无理由退货'])]
    private ?ReturnPolicyEnum $is7ToReturn = null;

    /**
     * 15天无理由退货
     */
    #[ORM\Column(type: Types::INTEGER, enumType: ReturnPolicyEnum::class, nullable: true, options: ['comment' => '15天无理由退货'])]
    private ?ReturnPolicyEnum $is15ToReturn = null;

    /**
     * 质保信息
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '质保信息'])]
    private ?string $warranty = null;

    /**
     * 商品产地
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '商品产地'])]
    private ?string $placeOfProduction = null;

    /**
     * 是否全球购
     */
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否全球购', 'default' => false])]
    private bool $isGlobalBuy = false;

    /**
     * 原产国
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '原产国'])]
    private ?string $originCountry = null;

    /**
     * 包装规格
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '包装规格'])]
    private ?string $packageType = null;

    /**
     * 商品长度(cm)
     */
    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => '商品长度(cm)'])]
    private ?float $length = null;

    /**
     * 商品宽度(cm)
     */
    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => '商品宽度(cm)'])]
    private ?float $width = null;

    /**
     * 商品高度(cm)
     */
    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => '商品高度(cm)'])]
    private ?float $height = null;

    /**
     * 销售单位
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '销售单位'])]
    private ?string $unit = null;

    /**
     * UPC码
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => 'UPC码'])]
    private ?string $upcCode = null;

    /**
     * 秒杀商品标识
     */
    #[ORM\Column(type: Types::INTEGER, enumType: FlashSaleEnum::class, nullable: true, options: ['comment' => '秒杀商品标识'])]
    private ?FlashSaleEnum $isFlashSale = null;

    /**
     * 秒杀价格
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '秒杀价格'])]
    private ?string $flashSalePrice = null;

    /**
     * 秒杀开始时间
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '秒杀开始时间'])]
    private ?\DateTimeInterface $flashSaleStartTime = null;

    /**
     * 秒杀结束时间
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '秒杀结束时间'])]
    private ?\DateTimeInterface $flashSaleEndTime = null;

    /**
     * 佣金比例
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true, options: ['comment' => '佣金比例'])]
    private ?string $commission = null;

    /**
     * 运费
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '运费'])]
    private ?string $fare = null;

    /**
     * 商品税率
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '商品税率'])]
    private ?string $tax = null;

    /**
     * 仓库ID
     */
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '仓库ID'])]
    private ?string $warehouseId = null;

    /**
     * 仓库名称
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '仓库名称'])]
    private ?string $warehouseName = null;

    /**
     * 保质期天数
     */
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '保质期天数'])]
    private ?int $shelfLife = null;

    // Getters and Setters
    public function getSkuId(): string
    {
        return $this->skuId;
    }

    public function setSkuId(string $skuId): self
    {
        $this->skuId = $skuId;
        return $this;
    }

    public function getSkuName(): string
    {
        return $this->skuName;
    }

    public function setSkuName(string $skuName): self
    {
        $this->skuName = $skuName;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
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

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(?string $categoryName): self
    {
        $this->categoryName = $categoryName;
        return $this;
    }

    public function getCategoryId1(): ?string
    {
        return $this->categoryId1;
    }

    public function setCategoryId1(?string $categoryId1): self
    {
        $this->categoryId1 = $categoryId1;
        return $this;
    }

    public function getCategoryName1(): ?string
    {
        return $this->categoryName1;
    }

    public function setCategoryName1(?string $categoryName1): self
    {
        $this->categoryName1 = $categoryName1;
        return $this;
    }

    public function getCategoryId2(): ?string
    {
        return $this->categoryId2;
    }

    public function setCategoryId2(?string $categoryId2): self
    {
        $this->categoryId2 = $categoryId2;
        return $this;
    }

    public function getCategoryName2(): ?string
    {
        return $this->categoryName2;
    }

    public function setCategoryName2(?string $categoryName2): self
    {
        $this->categoryName2 = $categoryName2;
        return $this;
    }

    public function getBrandId(): ?string
    {
        return $this->brandId;
    }

    public function setBrandId(?string $brandId): self
    {
        $this->brandId = $brandId;
        return $this;
    }

    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    public function setBrandName(?string $brandName): self
    {
        $this->brandName = $brandName;
        return $this;
    }

    public function getState(): SkuStateEnum
    {
        return $this->state;
    }

    public function setState(SkuStateEnum $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getSaleAttrs(): ?array
    {
        return $this->saleAttrs;
    }

    public function setSaleAttrs(?array $saleAttrs): self
    {
        $this->saleAttrs = $saleAttrs;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function getStockState(): StockStateEnum
    {
        return $this->stockState;
    }

    public function setStockState(StockStateEnum $stockState): self
    {
        $this->stockState = $stockState;
        return $this;
    }

    public function getOwnerType(): ?OwnerTypeEnum
    {
        return $this->ownerType;
    }

    public function setOwnerType(?OwnerTypeEnum $ownerType): self
    {
        $this->ownerType = $ownerType;
        return $this;
    }

    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    public function setShopName(?string $shopName): self
    {
        $this->shopName = $shopName;
        return $this;
    }

    public function getVendorName(): ?string
    {
        return $this->vendorName;
    }

    public function setVendorName(?string $vendorName): self
    {
        $this->vendorName = $vendorName;
        return $this;
    }

    public function getDeliveryType(): ?DeliveryTypeEnum
    {
        return $this->deliveryType;
    }

    public function setDeliveryType(?DeliveryTypeEnum $deliveryType): self
    {
        $this->deliveryType = $deliveryType;
        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    public function setDelivery(?string $delivery): self
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function getDeliveryAreas(): ?array
    {
        return $this->deliveryAreas;
    }

    public function setDeliveryAreas(?array $deliveryAreas): self
    {
        $this->deliveryAreas = $deliveryAreas;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getSpecs(): ?array
    {
        return $this->specs;
    }

    public function setSpecs(?array $specs): self
    {
        $this->specs = $specs;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getColorSequence(): ?string
    {
        return $this->colorSequence;
    }

    public function setColorSequence(?string $colorSequence): self
    {
        $this->colorSequence = $colorSequence;
        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getSizeSequence(): ?string
    {
        return $this->sizeSequence;
    }

    public function setSizeSequence(?string $sizeSequence): self
    {
        $this->sizeSequence = $sizeSequence;
        return $this;
    }

    public function getIs7ToReturn(): ?ReturnPolicyEnum
    {
        return $this->is7ToReturn;
    }

    public function setIs7ToReturn(?ReturnPolicyEnum $is7ToReturn): self
    {
        $this->is7ToReturn = $is7ToReturn;
        return $this;
    }

    public function getIs15ToReturn(): ?ReturnPolicyEnum
    {
        return $this->is15ToReturn;
    }

    public function setIs15ToReturn(?ReturnPolicyEnum $is15ToReturn): self
    {
        $this->is15ToReturn = $is15ToReturn;
        return $this;
    }

    public function getWarranty(): ?string
    {
        return $this->warranty;
    }

    public function setWarranty(?string $warranty): self
    {
        $this->warranty = $warranty;
        return $this;
    }

    public function getPlaceOfProduction(): ?string
    {
        return $this->placeOfProduction;
    }

    public function setPlaceOfProduction(?string $placeOfProduction): self
    {
        $this->placeOfProduction = $placeOfProduction;
        return $this;
    }

    public function isGlobalBuy(): bool
    {
        return $this->isGlobalBuy;
    }

    public function setIsGlobalBuy(bool $isGlobalBuy): self
    {
        $this->isGlobalBuy = $isGlobalBuy;
        return $this;
    }

    public function getOriginCountry(): ?string
    {
        return $this->originCountry;
    }

    public function setOriginCountry(?string $originCountry): self
    {
        $this->originCountry = $originCountry;
        return $this;
    }

    public function getPackageType(): ?string
    {
        return $this->packageType;
    }

    public function setPackageType(?string $packageType): self
    {
        $this->packageType = $packageType;
        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(?float $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    public function getUpcCode(): ?string
    {
        return $this->upcCode;
    }

    public function setUpcCode(?string $upcCode): self
    {
        $this->upcCode = $upcCode;
        return $this;
    }

    public function getIsFlashSale(): ?FlashSaleEnum
    {
        return $this->isFlashSale;
    }

    public function setIsFlashSale(?FlashSaleEnum $isFlashSale): self
    {
        $this->isFlashSale = $isFlashSale;
        return $this;
    }

    public function getFlashSalePrice(): ?string
    {
        return $this->flashSalePrice;
    }

    public function setFlashSalePrice(?string $flashSalePrice): self
    {
        $this->flashSalePrice = $flashSalePrice;
        return $this;
    }

    public function getFlashSaleStartTime(): ?\DateTimeInterface
    {
        return $this->flashSaleStartTime;
    }

    public function setFlashSaleStartTime(?\DateTimeInterface $flashSaleStartTime): self
    {
        $this->flashSaleStartTime = $flashSaleStartTime;
        return $this;
    }

    public function getFlashSaleEndTime(): ?\DateTimeInterface
    {
        return $this->flashSaleEndTime;
    }

    public function setFlashSaleEndTime(?\DateTimeInterface $flashSaleEndTime): self
    {
        $this->flashSaleEndTime = $flashSaleEndTime;
        return $this;
    }

    public function getCommission(): ?string
    {
        return $this->commission;
    }

    public function setCommission(?string $commission): self
    {
        $this->commission = $commission;
        return $this;
    }

    public function getFare(): ?string
    {
        return $this->fare;
    }

    public function setFare(?string $fare): self
    {
        $this->fare = $fare;
        return $this;
    }

    public function getTax(): ?string
    {
        return $this->tax;
    }

    public function setTax(?string $tax): self
    {
        $this->tax = $tax;
        return $this;
    }

    public function getWarehouseId(): ?string
    {
        return $this->warehouseId;
    }

    public function setWarehouseId(?string $warehouseId): self
    {
        $this->warehouseId = $warehouseId;
        return $this;
    }

    public function getWarehouseName(): ?string
    {
        return $this->warehouseName;
    }

    public function setWarehouseName(?string $warehouseName): self
    {
        $this->warehouseName = $warehouseName;
        return $this;
    }

    public function getShelfLife(): ?int
    {
        return $this->shelfLife;
    }

    public function setShelfLife(?int $shelfLife): self
    {
        $this->shelfLife = $shelfLife;
        return $this;
    }

    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        return [
            'skuId' => $this->skuId,
            'skuName' => $this->skuName,
            'price' => $this->price,
            'marketPrice' => $this->marketPrice,
            'categoryId' => $this->categoryId,
            'categoryName' => $this->categoryName,
            'categoryId1' => $this->categoryId1,
            'categoryName1' => $this->categoryName1,
            'categoryId2' => $this->categoryId2,
            'categoryName2' => $this->categoryName2,
            'brandId' => $this->brandId,
            'brandName' => $this->brandName,
            'state' => [
                'value' => $this->state->value,
                'label' => $this->state->getLabel(),
            ],
            'weight' => $this->weight,
            'saleAttrs' => $this->saleAttrs,
            'stock' => $this->stock,
            'stockState' => [
                'value' => $this->stockState->value,
                'label' => $this->stockState->getLabel(),
            ],
            'ownerType' => $this->ownerType !== null ? [
                'value' => $this->ownerType->value,
                'label' => $this->ownerType->getLabel(),
            ] : null,
            'shopName' => $this->shopName,
            'vendorName' => $this->vendorName,
            'deliveryType' => $this->deliveryType !== null ? [
                'value' => $this->deliveryType->value,
                'label' => $this->deliveryType->getLabel(),
            ] : null,
            'delivery' => $this->delivery,
            'deliveryAreas' => $this->deliveryAreas,
            'model' => $this->model,
            'specs' => $this->specs,
            'color' => $this->color,
            'colorSequence' => $this->colorSequence,
            'size' => $this->size,
            'sizeSequence' => $this->sizeSequence,
            'is7ToReturn' => $this->is7ToReturn !== null ? [
                'value' => $this->is7ToReturn->value,
                'label' => $this->is7ToReturn->getLabel(),
            ] : null,
            'is15ToReturn' => $this->is15ToReturn !== null ? [
                'value' => $this->is15ToReturn->value,
                'label' => $this->is15ToReturn->getLabel(),
            ] : null,
            'warranty' => $this->warranty,
            'placeOfProduction' => $this->placeOfProduction,
            'isGlobalBuy' => $this->isGlobalBuy,
            'originCountry' => $this->originCountry,
            'packageType' => $this->packageType,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'unit' => $this->unit,
            'upcCode' => $this->upcCode,
            'isFlashSale' => $this->isFlashSale !== null ? [
                'value' => $this->isFlashSale->value,
                'label' => $this->isFlashSale->getLabel(),
            ] : null,
            'flashSalePrice' => $this->flashSalePrice,
            'flashSaleStartTime' => $this->flashSaleStartTime?->format('Y-m-d H:i:s'),
            'flashSaleEndTime' => $this->flashSaleEndTime?->format('Y-m-d H:i:s'),
            'commission' => $this->commission,
            'fare' => $this->fare,
            'tax' => $this->tax,
            'warehouseId' => $this->warehouseId,
            'warehouseName' => $this->warehouseName,
            'shelfLife' => $this->shelfLife,
        ];
    }
}
