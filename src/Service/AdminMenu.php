<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Service;

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
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建京东云交易管理主菜单
        if (null === $item->getChild('京东云交易')) {
            $item->addChild('京东云交易')
                ->setAttribute('icon', 'fas fa-shopping-cart')
            ;
        }

        $jingdongMenu = $item->getChild('京东云交易');

        // 账户管理
        $jingdongMenu?->addChild('账户管理')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-user-cog')
        ;

        // 商品管理子菜单
        $jingdongMenu?->addChild('商品管理', ['uri' => '#'])
            ->setAttribute('icon', 'fas fa-boxes')
        ;

        $jingdongMenu?->getChild('商品管理')
            ?->addChild('商品分类')
            ->setUri($this->linkGenerator->getCurdListPage(Category::class))
            ->setAttribute('icon', 'fas fa-sitemap')
        ;

        $jingdongMenu?->getChild('商品管理')
            ?->addChild('商品SKU')
            ->setUri($this->linkGenerator->getCurdListPage(Sku::class))
            ->setAttribute('icon', 'fas fa-barcode')
        ;

        // 订单管理子菜单
        $jingdongMenu?->addChild('订单管理', ['uri' => '#'])
            ->setAttribute('icon', 'fas fa-shopping-bag')
        ;

        $jingdongMenu?->getChild('订单管理')
            ?->addChild('订单列表')
            ->setUri($this->linkGenerator->getCurdListPage(Order::class))
            ->setAttribute('icon', 'fas fa-list-alt')
        ;

        $jingdongMenu?->getChild('订单管理')
            ?->addChild('订单商品')
            ->setUri($this->linkGenerator->getCurdListPage(OrderItem::class))
            ->setAttribute('icon', 'fas fa-shopping-basket')
        ;

        $jingdongMenu?->getChild('订单管理')
            ?->addChild('订单评论')
            ->setUri($this->linkGenerator->getCurdListPage(Comment::class))
            ->setAttribute('icon', 'fas fa-comments')
        ;

        // 支付管理
        $jingdongMenu
            ?->addChild('支付管理')
            ->setUri($this->linkGenerator->getCurdListPage(Payment::class))
            ->setAttribute('icon', 'fas fa-credit-card')
        ;

        // 物流管理子菜单
        $jingdongMenu?->addChild('物流管理', ['uri' => '#'])
            ->setAttribute('icon', 'fas fa-truck')
        ;

        $jingdongMenu?->getChild('物流管理')
            ?->addChild('物流信息')
            ->setUri($this->linkGenerator->getCurdListPage(Logistics::class))
            ->setAttribute('icon', 'fas fa-shipping-fast')
        ;

        $jingdongMenu?->getChild('物流管理')
            ?->addChild('收货地址')
            ->setUri($this->linkGenerator->getCurdListPage(DeliveryAddress::class))
            ->setAttribute('icon', 'fas fa-map-marker-alt')
        ;

        // 售后管理
        $jingdongMenu
            ?->addChild('售后服务')
            ->setUri($this->linkGenerator->getCurdListPage(AfsService::class))
            ->setAttribute('icon', 'fas fa-tools')
        ;

        // 系统管理子菜单
        $jingdongMenu?->addChild('系统管理', ['uri' => '#'])
            ->setAttribute('icon', 'fas fa-cogs')
        ;

        $jingdongMenu?->getChild('系统管理')
            ?->addChild('地区管理')
            ->setUri($this->linkGenerator->getCurdListPage(Area::class))
            ->setAttribute('icon', 'fas fa-globe')
        ;

        $jingdongMenu?->getChild('系统管理')
            ?->addChild('发票管理')
            ->setUri($this->linkGenerator->getCurdListPage(Invoice::class))
            ->setAttribute('icon', 'fas fa-file-invoice')
        ;
    }
}
