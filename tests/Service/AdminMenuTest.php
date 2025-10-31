<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\AfsService;
use JingdongCloudTradeBundle\Entity\Area;
use JingdongCloudTradeBundle\Entity\Category;
use JingdongCloudTradeBundle\Entity\Comment;
use JingdongCloudTradeBundle\Entity\DeliveryAddress;
use JingdongCloudTradeBundle\Entity\Invoice;
use JingdongCloudTradeBundle\Entity\Logistics;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\OrderItem;
use JingdongCloudTradeBundle\Entity\Payment;
use JingdongCloudTradeBundle\Entity\Sku;
use JingdongCloudTradeBundle\Service\AdminMenu;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private MenuFactory $menuFactory;

    private LinkGeneratorInterface $linkGenerator;

    private AdminMenu $adminMenu;

    protected function onSetUp(): void
    {
        $this->menuFactory = new MenuFactory();
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);

        // 设置所有可能的mock返回值
        $this->linkGenerator->method('getCurdListPage')->willReturnMap([
            [Account::class, '/admin/account'],
            [Category::class, '/admin/category'],
            [Sku::class, '/admin/sku'],
            [Order::class, '/admin/order'],
            [OrderItem::class, '/admin/order-item'],
            [Comment::class, '/admin/comment'],
            [Payment::class, '/admin/payment'],
            [Logistics::class, '/admin/logistics'],
            [DeliveryAddress::class, '/admin/delivery-address'],
            [AfsService::class, '/admin/afs-service'],
            [Area::class, '/admin/area'],
            [Invoice::class, '/admin/invoice'],
        ]);

        // 将Mock注入服务容器
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testAdminMenuCreatesMainMenu(): void
    {
        /** @var InvocationMocker $method */
        $method = $this->linkGenerator->method('getCurdListPage');
        $method->willReturn('/admin/test');

        $rootMenu = $this->menuFactory->createItem('root');
        ($this->adminMenu)($rootMenu);

        $this->assertTrue($rootMenu->hasChildren());
        $this->assertNotNull($rootMenu->getChild('京东云交易'));

        $jingdongMenu = $rootMenu->getChild('京东云交易');
        $this->assertNotNull($jingdongMenu);
        $this->assertEquals('fas fa-shopping-cart', $jingdongMenu->getAttribute('icon'));
    }

    public function testAdminMenuCreatesAccountManagement(): void
    {
        $rootMenu = $this->menuFactory->createItem('root');
        ($this->adminMenu)($rootMenu);

        $jingdongMenu = $rootMenu->getChild('京东云交易');
        $this->assertNotNull($jingdongMenu);
        $this->assertNotNull($jingdongMenu->getChild('账户管理'));

        $accountMenu = $jingdongMenu->getChild('账户管理');
        $this->assertNotNull($accountMenu);
        $this->assertEquals('/admin/account', $accountMenu->getUri());
        $this->assertEquals('fas fa-user-cog', $accountMenu->getAttribute('icon'));
    }

    public function testAdminMenuCreatesProductManagement(): void
    {
        $rootMenu = $this->menuFactory->createItem('root');
        ($this->adminMenu)($rootMenu);

        $jingdongMenu = $rootMenu->getChild('京东云交易');
        $this->assertNotNull($jingdongMenu);

        // 商品管理主菜单
        $this->assertNotNull($jingdongMenu->getChild('商品管理'));
        $productMenu = $jingdongMenu->getChild('商品管理');
        $this->assertNotNull($productMenu);
        $this->assertEquals('fas fa-boxes', $productMenu->getAttribute('icon'));

        // 商品分类
        $this->assertNotNull($productMenu->getChild('商品分类'));
        $categoryMenu = $productMenu->getChild('商品分类');
        $this->assertNotNull($categoryMenu);
        $this->assertEquals('/admin/category', $categoryMenu->getUri());

        // 商品SKU
        $this->assertNotNull($productMenu->getChild('商品SKU'));
        $skuMenu = $productMenu->getChild('商品SKU');
        $this->assertNotNull($skuMenu);
        $this->assertEquals('/admin/sku', $skuMenu->getUri());
    }

    public function testAdminMenuCreatesOrderManagement(): void
    {
        $rootMenu = $this->menuFactory->createItem('root');
        ($this->adminMenu)($rootMenu);

        $jingdongMenu = $rootMenu->getChild('京东云交易');
        $this->assertNotNull($jingdongMenu);

        // 订单管理主菜单
        $this->assertNotNull($jingdongMenu->getChild('订单管理'));
        $orderMenu = $jingdongMenu->getChild('订单管理');
        $this->assertNotNull($orderMenu);
        $this->assertEquals('fas fa-shopping-bag', $orderMenu->getAttribute('icon'));

        // 订单列表
        $this->assertNotNull($orderMenu->getChild('订单列表'));
        $orderListMenu = $orderMenu->getChild('订单列表');
        $this->assertNotNull($orderListMenu);
        $this->assertEquals('/admin/order', $orderListMenu->getUri());

        // 订单商品
        $this->assertNotNull($orderMenu->getChild('订单商品'));
        $orderItemMenu = $orderMenu->getChild('订单商品');
        $this->assertNotNull($orderItemMenu);
        $this->assertEquals('/admin/order-item', $orderItemMenu->getUri());

        // 订单评论
        $this->assertNotNull($orderMenu->getChild('订单评论'));
        $commentMenu = $orderMenu->getChild('订单评论');
        $this->assertNotNull($commentMenu);
        $this->assertEquals('/admin/comment', $commentMenu->getUri());
    }

    public function testAdminMenuCreatesPaymentManagement(): void
    {
        $rootMenu = $this->menuFactory->createItem('root');
        ($this->adminMenu)($rootMenu);

        $jingdongMenu = $rootMenu->getChild('京东云交易');
        $this->assertNotNull($jingdongMenu);
        $this->assertNotNull($jingdongMenu->getChild('支付管理'));

        $paymentMenu = $jingdongMenu->getChild('支付管理');
        $this->assertNotNull($paymentMenu);
        $this->assertEquals('/admin/payment', $paymentMenu->getUri());
        $this->assertEquals('fas fa-credit-card', $paymentMenu->getAttribute('icon'));
    }

    public function testAdminMenuCreatesLogisticsManagement(): void
    {
        $rootMenu = $this->menuFactory->createItem('root');
        ($this->adminMenu)($rootMenu);

        $jingdongMenu = $rootMenu->getChild('京东云交易');
        $this->assertNotNull($jingdongMenu);

        // 物流管理主菜单
        $this->assertNotNull($jingdongMenu->getChild('物流管理'));
        $logisticsMenu = $jingdongMenu->getChild('物流管理');
        $this->assertNotNull($logisticsMenu);
        $this->assertEquals('fas fa-truck', $logisticsMenu->getAttribute('icon'));

        // 物流信息
        $this->assertNotNull($logisticsMenu->getChild('物流信息'));
        $logisticsInfoMenu = $logisticsMenu->getChild('物流信息');
        $this->assertNotNull($logisticsInfoMenu);
        $this->assertEquals('/admin/logistics', $logisticsInfoMenu->getUri());

        // 收货地址
        $this->assertNotNull($logisticsMenu->getChild('收货地址'));
        $addressMenu = $logisticsMenu->getChild('收货地址');
        $this->assertNotNull($addressMenu);
        $this->assertEquals('/admin/delivery-address', $addressMenu->getUri());
    }

    public function testAdminMenuCreatesAfterSalesService(): void
    {
        $rootMenu = $this->menuFactory->createItem('root');
        ($this->adminMenu)($rootMenu);

        $jingdongMenu = $rootMenu->getChild('京东云交易');
        $this->assertNotNull($jingdongMenu);
        $this->assertNotNull($jingdongMenu->getChild('售后服务'));

        $afsMenu = $jingdongMenu->getChild('售后服务');
        $this->assertNotNull($afsMenu);
        $this->assertEquals('/admin/afs-service', $afsMenu->getUri());
        $this->assertEquals('fas fa-tools', $afsMenu->getAttribute('icon'));
    }

    public function testAdminMenuCreatesSystemManagement(): void
    {
        $rootMenu = $this->menuFactory->createItem('root');
        ($this->adminMenu)($rootMenu);

        $jingdongMenu = $rootMenu->getChild('京东云交易');
        $this->assertNotNull($jingdongMenu);

        // 系统管理主菜单
        $this->assertNotNull($jingdongMenu->getChild('系统管理'));
        $systemMenu = $jingdongMenu->getChild('系统管理');
        $this->assertNotNull($systemMenu);
        $this->assertEquals('fas fa-cogs', $systemMenu->getAttribute('icon'));

        // 地区管理
        $this->assertNotNull($systemMenu->getChild('地区管理'));
        $areaMenu = $systemMenu->getChild('地区管理');
        $this->assertNotNull($areaMenu);
        $this->assertEquals('/admin/area', $areaMenu->getUri());

        // 发票管理
        $this->assertNotNull($systemMenu->getChild('发票管理'));
        $invoiceMenu = $systemMenu->getChild('发票管理');
        $this->assertNotNull($invoiceMenu);
        $this->assertEquals('/admin/invoice', $invoiceMenu->getUri());
    }
}
