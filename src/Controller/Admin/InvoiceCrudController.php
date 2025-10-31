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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use JingdongCloudTradeBundle\Entity\Invoice;
use JingdongCloudTradeBundle\Enum\InvoiceContentEnum;
use JingdongCloudTradeBundle\Enum\InvoiceStateEnum;
use JingdongCloudTradeBundle\Enum\InvoiceTitleTypeEnum;
use JingdongCloudTradeBundle\Enum\InvoiceTypeEnum;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<Invoice>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/invoice', routeName: 'jingdong_cloud_trade_invoice')]
final class InvoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invoice::class;
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
            ChoiceField::new('invoiceType', '发票类型')
                ->setChoices([
                    '普通发票' => InvoiceTypeEnum::NORMAL,
                    '增值税发票' => InvoiceTypeEnum::VAT,
                    '电子发票' => InvoiceTypeEnum::ELECTRONIC,
                ])
                ->setRequired(true)
                ->formatValue(function ($value) {
                    return $value instanceof InvoiceTypeEnum ? $value->getLabel() : '';
                })
                ->setHelp('发票类型枚举值'),
            ChoiceField::new('titleType', '抬头类型')
                ->setChoices([
                    '个人' => InvoiceTitleTypeEnum::PERSONAL,
                    '企业' => InvoiceTitleTypeEnum::COMPANY,
                ])
                ->setRequired(true)
                ->formatValue(function ($value) {
                    return $value instanceof InvoiceTitleTypeEnum ? $value->getLabel() : '';
                })
                ->setHelp('发票抬头类型枚举值'),
            TextField::new('title', '发票抬头')
                ->setRequired(true),
            TextField::new('taxpayerIdentity', '纳税人识别号')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('registeredAddress', '注册地址')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('registeredPhone', '注册电话')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('bankName', '开户银行')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('bankAccount', '银行账户')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('invoiceCode', '发票代码')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('invoiceNumber', '发票号码')
                ->setRequired(false)
                ->hideOnIndex(),
            MoneyField::new('invoiceAmount', '发票金额')
                ->setCurrency('CNY')
                ->setRequired(false),
            ChoiceField::new('invoiceState', '发票状态')
                ->setChoices([
                    '未申请' => InvoiceStateEnum::NOT_APPLIED,
                    '申请中' => InvoiceStateEnum::PENDING,
                    '已开票' => InvoiceStateEnum::ISSUED,
                    '开票失败' => InvoiceStateEnum::FAILED,
                    '已取消' => InvoiceStateEnum::CANCELLED,
                ])
                ->setRequired(false)
                ->formatValue(function ($value) {
                    return $value instanceof InvoiceStateEnum ? $value->getLabel() : '';
                })
                ->setHelp('发票状态枚举值'),
            ChoiceField::new('invoiceContent', '发票内容')
                ->setChoices([
                    '商品明细' => InvoiceContentEnum::GOODS,
                    '商品类别' => InvoiceContentEnum::CATEGORY,
                    '自定义' => InvoiceContentEnum::CUSTOM,
                ])
                ->setRequired(false)
                ->formatValue(function ($value) {
                    return $value instanceof InvoiceContentEnum ? $value->getLabel() : '';
                })
                ->setHelp('发票内容枚举值')
                ->hideOnIndex(),
            UrlField::new('downloadUrl', '电子发票下载链接')
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
