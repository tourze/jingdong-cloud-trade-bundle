<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Enum\OrderStateEnum;
use JingdongCloudTradeBundle\Enum\PaymentStateEnum;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<Order>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/order', routeName: 'jingdong_cloud_trade_order')]
final class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // 注意：订单状态和支付状态现在已实现BadgeInterface，
        // 可以使用ChoiceField来显示带颜色的状态标识。
        // 示例代码：
        // ChoiceField::new('orderState', '订单状态')
        //     ->setChoices(OrderStateEnum::toSelectChoices())
        //     ->renderAsBadges(),
        // ChoiceField::new('paymentState', '支付状态')
        //     ->setChoices(PaymentStateEnum::toSelectChoices())
        //     ->renderAsBadges(),

        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('orderId', '京东订单号')
                ->setRequired(true)
                ->setHelp('京东系统生成的订单号'),
            AssociationField::new('account', '京东账户')
                ->setRequired(true)
                ->hideOnIndex(),
            TextField::new('orderState', '订单状态')
                ->setRequired(true),
            TextField::new('paymentState', '支付状态')
                ->setRequired(true),
            TextField::new('logisticsState', '物流状态')
                ->setRequired(true),
            TextField::new('receiverName', '收货人姓名')
                ->setRequired(true),
            TextField::new('receiverMobile', '收货人手机号')
                ->setRequired(true),
            TextField::new('receiverProvince', '收货省份')
                ->setRequired(true)
                ->hideOnIndex(),
            TextField::new('receiverCity', '收货城市')
                ->setRequired(true)
                ->hideOnIndex(),
            TextField::new('receiverCounty', '收货区县')
                ->setRequired(true)
                ->hideOnIndex(),
            TextField::new('receiverAddress', '收货详细地址')
                ->setRequired(true)
                ->hideOnIndex(),
            MoneyField::new('orderTotalPrice', '订单总金额')
                ->setCurrency('CNY')
                ->setRequired(true),
            MoneyField::new('orderPaymentPrice', '实付金额')
                ->setCurrency('CNY')
                ->setRequired(true),
            MoneyField::new('freightPrice', '运费')
                ->setCurrency('CNY')
                ->setRequired(true)
                ->hideOnIndex(),
            DateTimeField::new('orderTime', '下单时间')
                ->setRequired(true),
            DateTimeField::new('paymentTime', '支付时间')
                ->setRequired(false)
                ->hideOnIndex(),
            DateTimeField::new('deliveryTime', '发货时间')
                ->setRequired(false)
                ->hideOnIndex(),
            DateTimeField::new('completionTime', '完成时间')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('waybillCode', '运单号')
                ->setRequired(false)
                ->hideOnIndex(),
            BooleanField::new('synced', '已同步到京东')
                ->hideOnIndex(),
            CollectionField::new('items', '订单商品')
                ->hideOnIndex()
                ->hideOnForm(),
            DateTimeField::new('createTime', '创建时间')->hideOnForm(),
            DateTimeField::new('updateTime', '更新时间')->hideOnForm(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud;
    }
}
