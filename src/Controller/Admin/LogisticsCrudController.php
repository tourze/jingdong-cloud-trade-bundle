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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JingdongCloudTradeBundle\Entity\Logistics;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<Logistics>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/logistics', routeName: 'jingdong_cloud_trade_logistics')]
final class LogisticsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Logistics::class;
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
            TextField::new('logisticsCode', '物流公司编码')
                ->setRequired(true),
            TextField::new('logisticsName', '物流公司名称')
                ->setRequired(true),
            TextField::new('waybillCode', '物流单号')
                ->setRequired(true)
                ->setHelp('快递单号'),
            TextareaField::new('trackInfo', '物流轨迹信息')
                ->setRequired(false)
                ->setHelp('JSON格式的物流轨迹信息')
                ->hideOnIndex(),
            DateTimeField::new('lastModificationTime', '最后更新时间')
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
