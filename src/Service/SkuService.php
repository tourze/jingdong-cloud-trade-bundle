<?php

namespace JingdongCloudTradeBundle\Service;

use JingdongCloudTradeBundle\Entity\Sku;

/**
 * SKU服务类，提供Sku实体的数据格式化方法
 */
class SkuService
{
    /**
     * 转换SKU为面向前端的数组
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
        if ($sku->getBookInfo()->getIsbn()) {
            $data['bookInfo'] = $sku->getBookInfo()->toArray();
        }
        
        return $data;
    }
    
    /**
     * 转换为JSON可序列化的数组
     */
    public function toJsonArray(Sku $sku): array
    {
        return $this->toPlainArray($sku);
    }
    
    /**
     * 从API响应数据中填充SKU实体
     * 
     * @param Sku $sku 要填充的SKU实体
     * @param array $data API响应数据
     */
    public function fillSkuFromApiData(Sku $sku, array $data): void
    {
        // 提取并填充基本信息
        if (isset($data['skuBaseInfo'])) {
            $this->fillSkuBaseInfo($sku, $data['skuBaseInfo']);
        }
        
        // 提取并填充图片信息
        if (isset($data['imageInfos'])) {
            $this->fillSkuImageInfo($sku, $data['imageInfos'], $data['skuBaseInfo']['imgUrl'] ?? null);
        }
        
        // 提取并填充规格信息
        if (isset($data['specifications'])) {
            $this->fillSkuSpecificationInfo($sku, $data['specifications'], $data['extAtts'] ?? []);
        }
        
        // 提取并填充大字段信息
        if (isset($data['skuBigFieldInfo'])) {
            $this->fillSkuBigFieldInfo($sku, $data['skuBigFieldInfo']);
        }
        
        // 提取并填充图书信息
        if (isset($data['skuBaseInfo']['bookSkuBaseInfo'])) {
            $this->fillSkuBookInfo($sku, $data['skuBaseInfo']['bookSkuBaseInfo']);
        }
    }
    
    /**
     * 填充SKU基础信息
     */
    private function fillSkuBaseInfo(Sku $sku, array $baseInfo): void
    {
        $skuBaseInfo = $sku->getBaseInfo();
        
        // 基础信息映射
        if (isset($baseInfo['skuId'])) {
            $skuBaseInfo->setSkuId($baseInfo['skuId']);
        }
        if (isset($baseInfo['skuName'])) {
            $skuBaseInfo->setSkuName($baseInfo['skuName']);
        }
        if (isset($baseInfo['price'])) {
            $skuBaseInfo->setPrice($baseInfo['price']);
        }
        if (isset($baseInfo['marketPrice'])) {
            $skuBaseInfo->setMarketPrice($baseInfo['marketPrice']);
        }
        
        // 分类信息
        if (isset($baseInfo['categoryId'])) {
            $skuBaseInfo->setCategoryId($baseInfo['categoryId']);
        }
        if (isset($baseInfo['categoryName'])) {
            $skuBaseInfo->setCategoryName($baseInfo['categoryName']);
        }
        if (isset($baseInfo['categoryId1'])) {
            $skuBaseInfo->setCategoryId1($baseInfo['categoryId1']);
        }
        if (isset($baseInfo['categoryName1'])) {
            $skuBaseInfo->setCategoryName1($baseInfo['categoryName1']);
        }
        if (isset($baseInfo['categoryId2'])) {
            $skuBaseInfo->setCategoryId2($baseInfo['categoryId2']);
        }
        if (isset($baseInfo['categoryName2'])) {
            $skuBaseInfo->setCategoryName2($baseInfo['categoryName2']);
        }
        
        // 品牌信息
        if (isset($baseInfo['brandId'])) {
            $skuBaseInfo->setBrandId($baseInfo['brandId']);
        }
        if (isset($baseInfo['brandName'])) {
            $skuBaseInfo->setBrandName($baseInfo['brandName']);
        }
        
        // 商品状态
        if (isset($baseInfo['skuStatus'])) {
            $skuBaseInfo->setState($baseInfo['skuStatus']);
        }
        
        // 重量
        if (isset($baseInfo['weight'])) {
            $skuBaseInfo->setWeight((int)$baseInfo['weight']);
        }
        
        // 销售属性
        if (isset($baseInfo['saleAttributesList'])) {
            $skuBaseInfo->setSaleAttrs($baseInfo['saleAttributesList']);
        }
        
        // 其他基础信息
        if (isset($baseInfo['venderName'])) {
            $skuBaseInfo->setVendorName($baseInfo['venderName']);
        }
        if (isset($baseInfo['shopName'])) {
            $skuBaseInfo->setShopName($baseInfo['shopName']);
        }
        if (isset($baseInfo['delivery'])) {
            $skuBaseInfo->setDelivery($baseInfo['delivery']);
        }
        if (isset($baseInfo['unit'])) {
            $skuBaseInfo->setUnit($baseInfo['unit']);
        }
        if (isset($baseInfo['model'])) {
            $skuBaseInfo->setModel($baseInfo['model']);
        }
        if (isset($baseInfo['color'])) {
            $skuBaseInfo->setColor($baseInfo['color']);
        }
        if (isset($baseInfo['colorSequence'])) {
            $skuBaseInfo->setColorSequence($baseInfo['colorSequence']);
        }
        if (isset($baseInfo['size'])) {
            $skuBaseInfo->setSize($baseInfo['size']);
        }
        if (isset($baseInfo['sizeSequence'])) {
            $skuBaseInfo->setSizeSequence($baseInfo['sizeSequence']);
        }
        if (isset($baseInfo['packageType'])) {
            $skuBaseInfo->setPackageType($baseInfo['packageType']);
        }
        if (isset($baseInfo['warranty'])) {
            $skuBaseInfo->setWarranty($baseInfo['warranty']);
        }
        if (isset($baseInfo['placeOfProduction'])) {
            $skuBaseInfo->setPlaceOfProduction($baseInfo['placeOfProduction']);
        }
        if (isset($baseInfo['fare'])) {
            $skuBaseInfo->setFare($baseInfo['fare']);
        }
        if (isset($baseInfo['tax'])) {
            $skuBaseInfo->setTax($baseInfo['tax']);
        }
        if (isset($baseInfo['width'])) {
            $skuBaseInfo->setWidth((float)$baseInfo['width']);
        }
        if (isset($baseInfo['height'])) {
            $skuBaseInfo->setHeight((float)$baseInfo['height']);
        }
        if (isset($baseInfo['length'])) {
            $skuBaseInfo->setLength((float)$baseInfo['length']);
        }
        if (isset($baseInfo['wareType']) && $baseInfo['wareType'] === '2') {
            $skuBaseInfo->setIsGlobalBuy(true);
        }
        if (isset($baseInfo['shelfLife'])) {
            $skuBaseInfo->setShelfLife((int)$baseInfo['shelfLife']);
        }
        if (isset($baseInfo['upcCode'])) {
            $skuBaseInfo->setUpcCode($baseInfo['upcCode']);
        }
    }
    
    /**
     * 填充SKU图片信息
     */
    private function fillSkuImageInfo(Sku $sku, array $imageInfos, ?string $imgUrl = null): void
    {
        $skuImageInfo = $sku->getImageInfo();
        
        if (isset($imageInfos)) {
            $skuImageInfo->setImageInfos($imageInfos);
        }
        
        // 如果有图片信息，提取主图URL
        if (!empty($imageInfos)) {
            foreach ($imageInfos as $img) {
                if (isset($img['isPrimary']) && $img['isPrimary'] === '1' && isset($img['path'])) {
                    $skuImageInfo->setImageUrl($img['path']);
                    break;
                }
            }
        }
        
        // 如果没找到主图，但baseInfo中有imgUrl
        if ($skuImageInfo->getImageUrl() === null && $imgUrl !== null) {
            $skuImageInfo->setImageUrl($imgUrl);
        }
    }
    
    /**
     * 填充SKU规格信息
     */
    private function fillSkuSpecificationInfo(Sku $sku, array $specifications, array $extAttributes = []): void
    {
        $skuSpecification = $sku->getSpecification();
        
        $skuSpecification->setSpecifications($specifications);
        $skuSpecification->setExtAttributes($extAttributes);
    }
    
    /**
     * 填充SKU大字段信息
     */
    private function fillSkuBigFieldInfo(Sku $sku, array $bigFieldInfo): void
    {
        $skuBigFieldInfo = $sku->getBigFieldInfo();
        
        if (isset($bigFieldInfo['pcWdis'])) {
            $skuBigFieldInfo->setPcWdis($bigFieldInfo['pcWdis']);
        }
        if (isset($bigFieldInfo['pcHtmlContent'])) {
            $skuBigFieldInfo->setPcHtmlContent($bigFieldInfo['pcHtmlContent']);
        }
        if (isset($bigFieldInfo['pcJsContent'])) {
            $skuBigFieldInfo->setPcJsContent($bigFieldInfo['pcJsContent']);
        }
        if (isset($bigFieldInfo['pcCssContent'])) {
            $skuBigFieldInfo->setPcCssContent($bigFieldInfo['pcCssContent']);
        }
    }
    
    /**
     * 填充SKU图书信息
     */
    private function fillSkuBookInfo(Sku $sku, array $bookInfo): void
    {
        $skuBookInfo = $sku->getBookInfo();
        
        if (isset($bookInfo['id'])) {
            $skuBookInfo->setId($bookInfo['id']);
        }
        if (isset($bookInfo['ISBN'])) {
            $skuBookInfo->setIsbn($bookInfo['ISBN']);
        }
        if (isset($bookInfo['ISSN'])) {
            $skuBookInfo->setIssn($bookInfo['ISSN']);
        }
        if (isset($bookInfo['barCode'])) {
            $skuBookInfo->setBarCode($bookInfo['barCode']);
        }
        if (isset($bookInfo['bookName'])) {
            $skuBookInfo->setBookName($bookInfo['bookName']);
        }
        if (isset($bookInfo['foreignBookName'])) {
            $skuBookInfo->setForeignBookName($bookInfo['foreignBookName']);
        }
        if (isset($bookInfo['author'])) {
            $skuBookInfo->setAuthor($bookInfo['author']);
        }
        if (isset($bookInfo['transfer'])) {
            $skuBookInfo->setTransfer($bookInfo['transfer']);
        }
        if (isset($bookInfo['editer'])) {
            $skuBookInfo->setEditer($bookInfo['editer']);
        }
        if (isset($bookInfo['compile'])) {
            $skuBookInfo->setCompile($bookInfo['compile']);
        }
        if (isset($bookInfo['drawer'])) {
            $skuBookInfo->setDrawer($bookInfo['drawer']);
        }
        if (isset($bookInfo['photography'])) {
            $skuBookInfo->setPhotography($bookInfo['photography']);
        }
        if (isset($bookInfo['proofreader'])) {
            $skuBookInfo->setProofreader($bookInfo['proofreader']);
        }
        if (isset($bookInfo['publishers'])) {
            $skuBookInfo->setPublishers($bookInfo['publishers']);
        }
        if (isset($bookInfo['publishNo'])) {
            $skuBookInfo->setPublishNo($bookInfo['publishNo']);
        }
        if (isset($bookInfo['publishTime'])) {
            $skuBookInfo->setPublishTime($bookInfo['publishTime']);
        }
        if (isset($bookInfo['printTime'])) {
            $skuBookInfo->setPrintTime($bookInfo['printTime']);
        }
        if (isset($bookInfo['batchNo'])) {
            $skuBookInfo->setBatchNo($bookInfo['batchNo']);
        }
        if (isset($bookInfo['printNo'])) {
            $skuBookInfo->setPrintNo($bookInfo['printNo']);
        }
        if (isset($bookInfo['pages'])) {
            $skuBookInfo->setPages($bookInfo['pages']);
        }
        if (isset($bookInfo['letters'])) {
            $skuBookInfo->setLetters($bookInfo['letters']);
        }
        if (isset($bookInfo['series'])) {
            $skuBookInfo->setSeries($bookInfo['series']);
        }
        if (isset($bookInfo['language'])) {
            $skuBookInfo->setLanguage($bookInfo['language']);
        }
        if (isset($bookInfo['sizeAndHeight'])) {
            $skuBookInfo->setSizeAndHeight($bookInfo['sizeAndHeight']);
        }
        if (isset($bookInfo['packageStr'])) {
            $skuBookInfo->setPackageStr($bookInfo['packageStr']);
        }
        if (isset($bookInfo['format'])) {
            $skuBookInfo->setFormat($bookInfo['format']);
        }
        if (isset($bookInfo['packNum'])) {
            $skuBookInfo->setPackNum((int)$bookInfo['packNum']);
        }
        if (isset($bookInfo['attachment'])) {
            $skuBookInfo->setAttachment($bookInfo['attachment']);
        }
        if (isset($bookInfo['attachmentNum'])) {
            $skuBookInfo->setAttachmentNum((int)$bookInfo['attachmentNum']);
        }
        if (isset($bookInfo['brand'])) {
            $skuBookInfo->setBrand($bookInfo['brand']);
        }
        if (isset($bookInfo['picNo'])) {
            $skuBookInfo->setPicNo($bookInfo['picNo']);
        }
        if (isset($bookInfo['chinaCatalog'])) {
            $skuBookInfo->setChinaCatalog($bookInfo['chinaCatalog']);
        }
        if (isset($bookInfo['marketPrice'])) {
            $skuBookInfo->setMarketPrice($bookInfo['marketPrice']);
        }
        if (isset($bookInfo['remarker'])) {
            $skuBookInfo->setRemarker($bookInfo['remarker']);
        }
    }
} 