<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Category;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Category::class)]
final class CategoryTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');

        $category = new Category();
        $category->setAccount($account);
        $category->setCategoryId('12345');
        $category->setCategoryName('Test Category');
        $category->setLevel(1);

        return $category;
    }

    public static function propertiesProvider(): iterable
    {
        // 由于 account 是关联实体，在属性测试中跳过
        yield 'categoryId' => ['categoryId', '67890'];
        yield 'categoryName' => ['categoryName', 'Updated Category'];
        yield 'parentId' => ['parentId', '12345'];
        yield 'level' => ['level', 2];
        yield 'state' => ['state', '1'];
        yield 'icon' => ['icon', 'https://example.com/icon.png'];
        yield 'sort' => ['sort', 10];
        yield 'description' => ['description', 'Test category description'];
        yield 'extraInfo' => ['extraInfo', ['key' => 'value']];
    }
}
