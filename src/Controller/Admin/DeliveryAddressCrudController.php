<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JingdongCloudTradeBundle\Entity\DeliveryAddress;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<DeliveryAddress>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/delivery-address', routeName: 'jingdong_cloud_trade_delivery_address')]
final class DeliveryAddressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DeliveryAddress::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            AssociationField::new('account', '京东账户')
                ->setRequired(true)
                ->hideOnIndex(),
            TextField::new('receiverName', '收货人姓名')
                ->setRequired(true),
            TextField::new('receiverMobile', '收货人手机号')
                ->setRequired(true),
            TextField::new('receiverPhone', '收货人固话')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('province', '省份')
                ->setRequired(true),
            TextField::new('city', '城市')
                ->setRequired(true),
            TextField::new('county', '区县')
                ->setRequired(true),
            TextField::new('town', '街道/乡镇')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('detailAddress', '详细地址')
                ->setRequired(true)
                ->hideOnIndex(),
            TextField::new('postCode', '邮政编码')
                ->setRequired(false)
                ->hideOnIndex(),
            BooleanField::new('isDefault', '默认地址'),
            TextField::new('addressTag', '地址标签')
                ->setRequired(false)
                ->setHelp('如：家、公司等')
                ->hideOnIndex(),
            BooleanField::new('supportGlobalBuy', '支持全球购')
                ->hideOnIndex(),
            TextField::new('idCardNo', '身份证号')
                ->setRequired(false)
                ->setHelp('全球购必填')
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
