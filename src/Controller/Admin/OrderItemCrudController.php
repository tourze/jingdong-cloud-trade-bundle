<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JingdongCloudTradeBundle\Entity\OrderItem;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<OrderItem>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/order-item', routeName: 'jingdong_cloud_trade_order_item')]
final class OrderItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrderItem::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            AssociationField::new('account', '京东账户')
                ->setRequired(true)
                ->hideOnIndex(),
            AssociationField::new('order', '关联订单')
                ->setRequired(true),
            TextField::new('skuId', '商品ID(SkuId)')
                ->setRequired(true)
                ->setHelp('京东商品SKU ID'),
            TextField::new('skuName', '商品名称')
                ->setRequired(true),
            IntegerField::new('quantity', '商品数量')
                ->setRequired(true),
            MoneyField::new('price', '商品单价')
                ->setCurrency('CNY')
                ->setRequired(true),
            MoneyField::new('totalPrice', '商品总价')
                ->setCurrency('CNY')
                ->setRequired(true),
            ImageField::new('imageUrl', '商品图片')
                ->setBasePath('/')
                ->setUploadDir('public/uploads/images')
                ->setRequired(false)
                ->hideOnIndex(),
            TextareaField::new('attributes', '商品属性')
                ->setRequired(false)
                ->setHelp('JSON格式的商品属性信息')
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
