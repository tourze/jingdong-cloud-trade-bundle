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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use JingdongCloudTradeBundle\Entity\Category;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<Category>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/category', routeName: 'jingdong_cloud_trade_category')]
final class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            AssociationField::new('account', '京东账户')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('categoryId', '分类ID')
                ->setRequired(true)
                ->setHelp('京东分类ID'),
            TextField::new('categoryName', '分类名称')
                ->setRequired(true),
            TextField::new('parentId', '父分类ID')
                ->setRequired(false)
                ->hideOnIndex(),
            IntegerField::new('level', '分类层级')
                ->setRequired(true),
            ChoiceField::new('state', '状态')
                ->setChoices([
                    '有效' => '1',
                    '无效' => '0',
                ])
                ->setRequired(true),
            UrlField::new('icon', '分类图标URL')
                ->setRequired(false)
                ->hideOnIndex(),
            IntegerField::new('sort', '排序权重')
                ->setRequired(false)
                ->hideOnIndex(),
            BooleanField::new('isVirtual', '是否虚拟分类')
                ->hideOnIndex(),
            TextareaField::new('description', '分类描述')
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
