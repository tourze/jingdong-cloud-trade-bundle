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
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JingdongCloudTradeBundle\Entity\AfsService;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<AfsService>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/afs-service', routeName: 'jingdong_cloud_trade_afs_service')]
final class AfsServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AfsService::class;
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
            TextField::new('afsServiceId', '售后服务单号')
                ->setRequired(true)
                ->setHelp('京东售后服务单号'),
            TextField::new('afsType', '售后类型')
                ->setRequired(true)
                ->setHelp('10-退货，20-换货，30-维修'),
            TextField::new('afsServiceState', '服务单状态')
                ->setRequired(true),
            TextField::new('applyReason', '申请原因')
                ->setRequired(false)
                ->hideOnIndex(),
            TextareaField::new('applyDescription', '申请描述')
                ->setRequired(false)
                ->hideOnIndex(),
            DateTimeField::new('applyTime', '申请时间')
                ->setRequired(false)
                ->hideOnIndex(),
            DateTimeField::new('auditTime', '审核时间')
                ->setRequired(false)
                ->hideOnIndex(),
            DateTimeField::new('completeTime', '完成时间')
                ->setRequired(false)
                ->hideOnIndex(),
            MoneyField::new('refundAmount', '退款金额')
                ->setCurrency('CNY')
                ->setRequired(false),
            TextField::new('logisticsCompany', '物流公司')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('logisticsNo', '物流单号')
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
