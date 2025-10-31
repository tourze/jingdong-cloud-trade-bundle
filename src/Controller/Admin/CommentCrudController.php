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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use JingdongCloudTradeBundle\Entity\Comment;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<Comment>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/comment', routeName: 'jingdong_cloud_trade_comment')]
final class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
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
            AssociationField::new('orderItem', '订单商品')
                ->setRequired(true),
            ChoiceField::new('score', '评分')
                ->setChoices([
                    '1分' => '1',
                    '2分' => '2',
                    '3分' => '3',
                    '4分' => '4',
                    '5分' => '5',
                ])
                ->setRequired(true)
                ->setHelp('1分最低，5分最高'),
            TextareaField::new('content', '评论内容')
                ->setRequired(false)
                ->hideOnIndex(),
            BooleanField::new('isAnonymous', '匿名评论')
                ->hideOnIndex(),
            DateTimeField::new('commentTime', '评论时间')
                ->setRequired(true),
            BooleanField::new('isApproved', '通过审核'),
            DateTimeField::new('approveTime', '审核时间')
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
