<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JingdongCloudTradeBundle\Entity\Payment;
use JingdongCloudTradeBundle\Enum\PaymentChannelEnum;
use JingdongCloudTradeBundle\Enum\PaymentMethodEnum;
use JingdongCloudTradeBundle\Enum\PaymentStateEnum;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<Payment>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/payment', routeName: 'jingdong_cloud_trade_payment')]
final class PaymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Payment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            AssociationField::new('order', '关联订单')
                ->setRequired(true),
            TextField::new('paymentId', '支付流水号')
                ->setRequired(true)
                ->setHelp('支付系统生成的唯一流水号'),
            ChoiceField::new('paymentMethod', '支付方式')
                ->setChoices(fn () => PaymentMethodEnum::cases())
                ->renderAsBadges()
                ->setRequired(true)
                ->setHelp('支付方式枚举值'),
            ChoiceField::new('paymentChannel', '支付渠道')
                ->setChoices(fn () => PaymentChannelEnum::cases())
                ->renderAsBadges()
                ->setRequired(false)
                ->setHelp('具体的支付渠道')
                ->hideOnIndex(),
            MoneyField::new('paymentAmount', '支付金额')
                ->setCurrency('CNY')
                ->setRequired(true),
            ChoiceField::new('paymentState', '支付状态')
                ->setChoices(fn () => PaymentStateEnum::cases())
                ->renderAsBadges()
                ->setRequired(true)
                ->setHelp('支付状态枚举值'),
            DateTimeField::new('paymentTime', '支付时间')
                ->setRequired(false),
            TextareaField::new('paymentNote', '支付备注')
                ->setRequired(false)
                ->hideOnIndex(),
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
