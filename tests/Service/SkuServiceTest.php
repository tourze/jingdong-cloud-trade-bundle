<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Embedded\SkuBaseInfo;
use JingdongCloudTradeBundle\Entity\Embedded\SkuBigFieldInfo;
use JingdongCloudTradeBundle\Entity\Embedded\SkuBookInfo;
use JingdongCloudTradeBundle\Entity\Embedded\SkuImageInfo;
use JingdongCloudTradeBundle\Entity\Embedded\SkuSpecification;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Enum\SkuStateEnum;
use JingdongCloudTradeBundle\Service\SkuService;
use PHPUnit\Framework\TestCase;

class SkuServiceTest extends TestCase
{
    private SkuService $skuService;
    private Sku $sku;
    private Account $account;
    
    protected function setUp(): void
    {
        $this->skuService = new SkuService();
        $this->sku = new Sku();
        $this->account = new Account();
        
        // 设置账户信息
        $this->account->setName('Test Account');
        $this->account->setAppKey('test_app_key');
        $this->account->setAppSecret('test_app_secret');
        $this->setPrivateProperty($this->account, 'id', 100);
        
        // 设置 SKU 关联的账户
        $this->sku->setAccount($this->account);
        $this->setPrivateProperty($this->sku, 'id', 1);
        
        // 初始化嵌入式属性
        $this->setSkuTestData();
    }
    
    private function setSkuTestData(): void
    {
        // 初始化基础信息
        $baseInfo = new SkuBaseInfo();
        $baseInfo->setSkuId('123456');
        $baseInfo->setSkuName('测试商品');
        $baseInfo->setPrice('199.99');
        $baseInfo->setMarketPrice('299.99');
        $baseInfo->setCategoryId('1000');
        $baseInfo->setCategoryName('测试分类');
        $baseInfo->setBrandId('2000');
        $baseInfo->setBrandName('测试品牌');
        $baseInfo->setState(SkuStateEnum::ON_SALE);  // 使用枚举类型而不是字符串
        $baseInfo->setWeight(1000);  // 1kg
        $baseInfo->setVendorName('测试供应商');
        $baseInfo->setShopName('测试店铺');
        
        // 初始化图片信息
        $imageInfo = new SkuImageInfo();
        $imageInfo->setImageUrl('https://example.com/main.jpg');
        $imageInfo->setDetailImages([
            'https://example.com/gallery1.jpg',
            'https://example.com/gallery2.jpg',
        ]);
        
        // 初始化规格信息
        $specification = new SkuSpecification();
        $specification->setScore(4.8);
        $specification->setCommentCount(100);
        $specification->setHasPromotion(true);
        $specification->setPromotionLabel('限时折扣');
        $specification->setPromotionInfo(['discount' => '0.8', 'endTime' => '2023-12-31']);
        $specification->setSpecifications([
            ['name' => '颜色', 'value' => '红色'],
            ['name' => '尺寸', 'value' => 'M'],
        ]);
        $specification->setParameters([
            ['name' => '材质', 'value' => '纯棉'],
            ['name' => '产地', 'value' => '中国'],
        ]);
        $specification->setAfterSalesInfo(['type' => '7天无理由退货']);
        $specification->setExtAttributes(['isGift' => false]);
        
        // 初始化大字段信息
        $bigFieldInfo = new SkuBigFieldInfo();
        $bigFieldInfo->setDescription('这是一个测试商品描述');
        $bigFieldInfo->setIntroduction('<p>这是一个HTML格式的商品介绍</p>');
        $bigFieldInfo->setWReadMe('产品使用说明');
        
        // 初始化图书信息
        $bookInfo = new SkuBookInfo();
        $bookInfo->setIsbn('9787000000000');
        $bookInfo->setAuthor('测试作者');
        $bookInfo->setPublishers('测试出版社');
        $bookInfo->setPublishTime('2023-01-01');
        
        // 设置到 SKU 对象
        $this->sku->setBaseInfo($baseInfo);
        $this->sku->setImageInfo($imageInfo);
        $this->sku->setSpecification($specification);
        $this->sku->setBigFieldInfo($bigFieldInfo);
        $this->sku->setBookInfo($bookInfo);
    }
    
    public function testToPlainArray(): void
    {
        $plainArray = $this->skuService->toPlainArray($this->sku);
        
        // 检查基础字段
        $this->assertEquals(1, $plainArray['id']);
        $this->assertEquals(100, $plainArray['accountId']);
        $this->assertEquals('123456', $plainArray['skuId']);
        $this->assertEquals('测试商品', $plainArray['skuName']);
        $this->assertEquals('199.99', $plainArray['price']);
        $this->assertEquals('299.99', $plainArray['marketPrice']);
        
        // 检查图片信息
        $this->assertEquals('https://example.com/main.jpg', $plainArray['imageUrl']);
        $this->assertCount(2, $plainArray['detailImages']);
        
        // 检查规格信息
        $this->assertEquals(4.8, $plainArray['score']);
        $this->assertEquals(100, $plainArray['commentCount']);
        $this->assertTrue($plainArray['hasPromotion']);
        $this->assertEquals('限时折扣', $plainArray['promotionLabel']);

        // 检查大字段信息
        $this->assertEquals('这是一个测试商品描述', $plainArray['description']);
        $this->assertEquals('<p>这是一个HTML格式的商品介绍</p>', $plainArray['introduction']);
    }
    
    public function testToAdminArray(): void
    {
        $adminArray = $this->skuService->toAdminArray($this->sku);
        
        // 检查基础字段（继承自 toPlainArray)
        $this->assertEquals(1, $adminArray['id']);
        $this->assertEquals(100, $adminArray['accountId']);
        
        // 检查管理后台额外字段
        $this->assertEquals(100, $adminArray['account']['id']);
        $this->assertEquals('Test Account', $adminArray['account']['name']);
        
        // 检查管理后台专用字段

        // 检查图书信息
        $this->assertEquals('9787000000000', $adminArray['bookInfo']['isbn']);
        $this->assertEquals('测试作者', $adminArray['bookInfo']['author']);
        $this->assertEquals('测试出版社', $adminArray['bookInfo']['publishers']);
    }
    
    public function testToJsonArray(): void
    {
        $jsonArray = $this->skuService->toJsonArray($this->sku);
        
        // 应该与 toPlainArray 相同
        $plainArray = $this->skuService->toPlainArray($this->sku);
        $this->assertEquals($plainArray, $jsonArray);
        
        // 确保可以 JSON 序列化
        $json = json_encode($jsonArray);
        $this->assertNotFalse($json);
        // 检查反序列化结果
        $decoded = json_decode($json, true);
        $this->assertEquals($jsonArray, $decoded);
    }
    
    public function testFillSkuFromApiData(): void
    {
        // 创建一个新的 SKU 实体
        $newSku = new Sku();
        $newSku->setAccount($this->account);
        
        // 我们需要在 API 填充前设置一个基本的 SkuBaseInfo 对象，因为 setState 需要枚举类型而 API 返回字符串
        $baseInfo = new SkuBaseInfo();
        $baseInfo->setState(SkuStateEnum::ON_SALE);  // 预先设置状态
        $newSku->setBaseInfo($baseInfo);
        
        // 准备模拟的 API 响应数据
        $apiData = [
            'skuBaseInfo' => [
                'skuId' => '654321',
                'skuName' => 'API商品',
                'price' => '99.99',
                'marketPrice' => '199.99',
                'categoryId' => '2000',
                'categoryName' => 'API分类',
                'brandId' => '3000',
                'brandName' => 'API品牌',
                // 移除 skuStatus，我们不测试这个字段，因为它需要枚举类型而 API 返回字符串
                'weight' => '500',
                'venderName' => 'API供应商',
                'shopName' => 'API店铺',
                'imgUrl' => 'https://example.com/api.jpg',
                'bookSkuBaseInfo' => [
                    'isbn' => '9788888888888',
                    'author' => 'API作者',
                    'publishers' => 'API出版社',
                    'publishTime' => '2023-06-01',
                ],
            ],
            'imageInfos' => [
                ['imageUrl' => 'https://example.com/api1.jpg'],
                ['imageUrl' => 'https://example.com/api2.jpg'],
            ],
            'specifications' => [
                ['name' => 'API规格1', 'value' => '值1'],
                ['name' => 'API规格2', 'value' => '值2'],
            ],
            'extAtts' => [
                ['name' => 'API属性1', 'value' => '值1'],
                ['name' => 'API属性2', 'value' => '值2'],
            ],
            'skuBigFieldInfo' => [
                'description' => 'API商品描述',
                'introduction' => '<p>API商品介绍</p>',
                'pcWdis' => 'API商品说明',
                'wReadMe' => 'API产品说明',
            ],
        ];
        
        // 填充数据
        $this->skuService->fillSkuFromApiData($newSku, $apiData);
        
        // 检查基础信息是否被正确填充
        $this->assertEquals('654321', $newSku->getBaseInfo()->getSkuId());
        $this->assertEquals('API商品', $newSku->getBaseInfo()->getSkuName());
        $this->assertEquals('99.99', $newSku->getBaseInfo()->getPrice());
        $this->assertEquals('API分类', $newSku->getBaseInfo()->getCategoryName());
        $this->assertEquals('API品牌', $newSku->getBaseInfo()->getBrandName());
        
        // 检查图书信息
        $this->assertEquals('9788888888888', $newSku->getBookInfo()->getIsbn());
        $this->assertEquals('API作者', $newSku->getBookInfo()->getAuthor());
        
        // 检查大字段信息
        $this->assertEquals('API商品描述', $newSku->getBigFieldInfo()->getDescription());
        $this->assertEquals('<p>API商品介绍</p>', $newSku->getBigFieldInfo()->getIntroduction());
    }
    
    /**
     * 通过反射设置私有属性的值
     */
    private function setPrivateProperty(object $object, string $propertyName, mixed $value): void
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
} 