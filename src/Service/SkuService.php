<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service;

use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Service\DataProcessor\ArrayDataValidator;
use JingdongCloudTradeBundle\Service\DataProcessor\BaseInfoFillStrategy;
use JingdongCloudTradeBundle\Service\DataProcessor\BigFieldFillStrategy;
use JingdongCloudTradeBundle\Service\DataProcessor\BookInfoFillStrategy;
use JingdongCloudTradeBundle\Service\DataProcessor\ImageInfoFillStrategy;
use JingdongCloudTradeBundle\Service\DataProcessor\SkuDataFillStrategy;
use JingdongCloudTradeBundle\Service\DataProcessor\SpecificationFillStrategy;

/**
 * SKU服务类，提供Sku实体的数据格式化方法
 */
readonly class SkuService
{
    /** @var SkuDataFillStrategy[] */
    private array $fillStrategies;

    public function __construct()
    {
        $validator = new ArrayDataValidator();
        $this->fillStrategies = [
            new BaseInfoFillStrategy($validator),
            new ImageInfoFillStrategy($validator),
            new SpecificationFillStrategy($validator),
            new BigFieldFillStrategy($validator),
            new BookInfoFillStrategy($validator),
        ];
    }

    /**
     * 转换SKU为面向前端的数组
     *
     * @return array<string, mixed>
     */
    public function toPlainArray(Sku $sku): array
    {
        $data = [];

        // 基本信息
        $data = array_merge($data, $sku->getBaseInfo()->toArray());

        // 图片信息
        $data = array_merge($data, $sku->getImageInfo()->toArray());

        // 规格信息（选择性字段）
        $specData = $sku->getSpecification()->toArray();
        $data['score'] = $specData['score'];
        $data['commentCount'] = $specData['commentCount'];
        $data['hasPromotion'] = $specData['hasPromotion'];
        $data['promotionLabel'] = $specData['promotionLabel'];
        $data['promotionInfo'] = $specData['promotionInfo'];
        $data['specifications'] = $specData['specifications'];

        // 大字段信息（只包含前端需要的）
        $bigFieldData = $sku->getBigFieldInfo()->toArray();
        $data['description'] = $bigFieldData['description'];
        $data['introduction'] = $bigFieldData['introduction'];

        // ID和账号信息
        $data['id'] = $sku->getId();
        $data['accountId'] = $sku->getAccount()->getId();

        return $data;
    }

    /**
     * 转换SKU为面向管理后台的数组
     *
     * @return array<string, mixed>
     */
    public function toAdminArray(Sku $sku): array
    {
        $data = $this->toPlainArray($sku);

        // 关联账号信息
        $data['account'] = [
            'id' => $sku->getAccount()->getId(),
            'name' => $sku->getAccount()->getName(),
        ];

        // 管理后台需要展示的其他字段
        $specData = $sku->getSpecification()->toArray();
        $data['parameters'] = $specData['parameters'];
        $data['afterSalesInfo'] = $specData['afterSalesInfo'];
        $data['extAttributes'] = $specData['extAttributes'];

        // 图书信息（仅适用于图书）
        if (null !== $sku->getBookInfo()->getIsbn()) {
            $data['bookInfo'] = $sku->getBookInfo()->toArray();
        }

        return $data;
    }

    /**
     * 转换为JSON可序列化的数组
     *
     * @return array<string, mixed>
     */
    public function toJsonArray(Sku $sku): array
    {
        return $this->toPlainArray($sku);
    }

    /**
     * 从API响应数据中填充SKU实体
     *
     * @param Sku                  $sku  要填充的SKU实体
     * @param array<string, mixed> $data API响应数据
     */
    public function fillSkuFromApiData(Sku $sku, array $data): void
    {
        $sections = [
            'skuBaseInfo',
            'imageInfos',
            'specifications',
            'skuBigFieldInfo',
            'bookSkuBaseInfo',
        ];

        foreach ($sections as $section) {
            $this->processSectionData($sku, $data, $section);
        }
    }

    /**
     * 处理特定数据段
     *
     * @param array<string, mixed> $data
     */
    private function processSectionData(Sku $sku, array $data, string $section): void
    {
        foreach ($this->fillStrategies as $strategy) {
            if ($strategy->canHandle($data, $section)) {
                $strategy->fill($sku, $data, $section);
                break;
            }
        }
    }
}
