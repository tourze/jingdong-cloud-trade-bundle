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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JingdongCloudTradeBundle\Entity\Sku;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<Sku>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/sku', routeName: 'jingdong_cloud_trade_sku')]
final class SkuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sku::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            AssociationField::new('account', '京东账户')
                ->setRequired(true)
                ->hideOnIndex(),
            TextField::new('baseInfo.skuId', 'SKU ID')
                ->setRequired(true)
                ->setHelp('京东商品SKU ID'),
            TextField::new('baseInfo.skuName', '商品名称')
                ->setRequired(true),
            TextField::new('baseInfo.brandName', '品牌名称')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('baseInfo.categoryId', '分类ID')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('baseInfo.categoryName', '分类名称')
                ->setRequired(false)
                ->hideOnIndex(),
            MoneyField::new('baseInfo.price', '商品价格')
                ->setCurrency('CNY')
                ->setRequired(false),
            MoneyField::new('baseInfo.marketPrice', '市场价格')
                ->setCurrency('CNY')
                ->setRequired(false)
                ->hideOnIndex(),
            IntegerField::new('baseInfo.stock', '库存数量')
                ->setRequired(false),
            ChoiceField::new('baseInfo.stockState', '库存状态')
                ->setRequired(false)
                ->setHelp('库存状态枚举值')
                ->hideOnIndex()
                ->renderExpanded(false)
                ->renderAsBadges(),
            ChoiceField::new('baseInfo.state', '商品状态')
                ->setRequired(false)
                ->setHelp('商品状态枚举值')
                ->hideOnIndex()
                ->renderExpanded(false)
                ->renderAsBadges(),
            DateTimeField::new('detailUpdateTime', '详情更新时间')
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
