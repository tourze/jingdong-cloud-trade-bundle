<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\DeliveryAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddress::class)]
final class DeliveryAddressTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        // 创建关联实体
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test_app_key');
        $account->setAppSecret('test_app_secret');

        $deliveryAddress = new DeliveryAddress();
        $deliveryAddress->setAccount($account);
        $deliveryAddress->setReceiverName('张三');
        $deliveryAddress->setReceiverMobile('13800138000');
        $deliveryAddress->setProvince('北京市');
        $deliveryAddress->setCity('北京市');
        $deliveryAddress->setCounty('朝阳区');
        $deliveryAddress->setDetailAddress('某某街道123号');

        return $deliveryAddress;
    }

    public static function propertiesProvider(): iterable
    {
        yield 'receiverName' => ['receiverName', '李四'];
        yield 'receiverMobile' => ['receiverMobile', '13900139000'];
        yield 'receiverPhone' => ['receiverPhone', '010-12345678'];
        yield 'province' => ['province', '上海市'];
        yield 'city' => ['city', '上海市'];
        yield 'county' => ['county', '浦东新区'];
        yield 'town' => ['town', '某某街道'];
        yield 'detailAddress' => ['detailAddress', '某某小区123号楼'];
        yield 'postCode' => ['postCode', '200000'];
        yield 'addressTag' => ['addressTag', '家'];
        yield 'idCardNo' => ['idCardNo', '110101199001011234'];
    }
}
