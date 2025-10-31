<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JingdongCloudTradeBundle\Entity\Account;

/**
 * @template TEntity of object
 * @extends AbstractCrudController<Account>
 */
#[AdminCrud(routePath: '/jingdong-cloud-trade/account', routeName: 'jingdong_cloud_trade_account')]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('name', '应用名称')
                ->setRequired(true)
                ->setHelp('京东应用的名称'),
            TextField::new('appKey', 'AppKey')
                ->setRequired(true)
                ->setHelp('京东开放平台分配的AppKey'),
            TextField::new('appSecret', 'AppSecret')
                ->setRequired(true)
                ->setHelp('京东开放平台分配的AppSecret')
                ->hideOnIndex(),
            TextField::new('accessToken', 'AccessToken')
                ->setRequired(false)
                ->setHelp('访问令牌')
                ->hideOnIndex(),
            TextField::new('refreshToken', 'RefreshToken')
                ->setRequired(false)
                ->setHelp('刷新令牌')
                ->hideOnIndex(),
            DateTimeField::new('accessTokenExpireTime', 'AccessToken过期时间')
                ->setRequired(false)
                ->hideOnIndex(),
            DateTimeField::new('refreshTokenExpireTime', 'RefreshToken过期时间')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('code', '授权码')
                ->setRequired(false)
                ->hideOnIndex(),
            DateTimeField::new('codeExpireTime', '授权码过期时间')
                ->setRequired(false)
                ->hideOnIndex(),
            TextField::new('state', '状态码')
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
