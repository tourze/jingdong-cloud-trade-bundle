<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\DeliveryAddress;
use JingdongCloudTradeBundle\Repository\DeliveryAddressRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddressRepository::class)]
#[RunTestsInSeparateProcesses]
final class DeliveryAddressRepositoryTest extends AbstractRepositoryTestCase
{
    private DeliveryAddressRepository $repository;

    private Account $testAccount;

    protected function onSetUp(): void
    {
        // 彻底重置数据库连接状态，确保每个测试都从干净状态开始
        $connection = self::getEntityManager()->getConnection();

        // 强制关闭并重置连接
        if ($connection->isConnected()) {
            $connection->close();
        }

        // 清理连接状态并重新建立连接
        try {
            // 尝试执行一个简单查询来强制重连
            $connection->executeQuery('SELECT 1');
        } catch (\Exception $e) {
            // 如果查询失败，尝试重新连接
            try {
                $connection = self::getEntityManager()->getConnection();
                $connection->executeQuery('SELECT 1');
            } catch (\Exception $e2) {
                // 忽略连接异常，让测试自然进行
            }
        }
        $this->repository = $this->getRepository();

        $this->testAccount = new Account();
        $this->testAccount->setAppKey('test-app-key');
        $this->testAccount->setAppSecret('test-app-secret');
        $this->testAccount->setName('Test Account');
        $this->persistAndFlush($this->testAccount);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createDeliveryAddress(array $data = []): DeliveryAddress
    {
        $address = new DeliveryAddress();
        $address->setAccount($this->testAccount);

        $this->setRequiredFields($address, $data);
        $this->setOptionalFields($address, $data);

        $persistedAddress = $this->persistAndFlush($address);
        $this->assertInstanceOf(DeliveryAddress::class, $persistedAddress);

        return $persistedAddress;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setRequiredFields(DeliveryAddress $address, array $data): void
    {
        // String类型字段
        $receiverName = $data['receiverName'] ?? 'Test Receiver';
        $address->setReceiverName(is_string($receiverName) ? $receiverName : 'Test Receiver');

        $receiverMobile = $data['receiverMobile'] ?? '13800138000';
        $address->setReceiverMobile(is_string($receiverMobile) ? $receiverMobile : '13800138000');

        $province = $data['province'] ?? '北京市';
        $address->setProvince(is_string($province) ? $province : '北京市');

        $city = $data['city'] ?? '北京市';
        $address->setCity(is_string($city) ? $city : '北京市');

        $county = $data['county'] ?? '朝阳区';
        $address->setCounty(is_string($county) ? $county : '朝阳区');

        $detailAddress = $data['detailAddress'] ?? '测试地址123号';
        $address->setDetailAddress(is_string($detailAddress) ? $detailAddress : '测试地址123号');

        // Bool类型字段
        $isDefault = $data['isDefault'] ?? false;
        $address->setIsDefault(is_bool($isDefault) ? $isDefault : false);

        $supportGlobalBuy = $data['supportGlobalBuy'] ?? false;
        $address->setSupportGlobalBuy(is_bool($supportGlobalBuy) ? $supportGlobalBuy : false);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setOptionalFields(DeliveryAddress $address, array $data): void
    {
        $this->setOptionalStringField($address, $data, 'receiverPhone', 'setReceiverPhone');
        $this->setOptionalStringField($address, $data, 'town', 'setTown');
        $this->setOptionalStringField($address, $data, 'postCode', 'setPostCode');
        $this->setOptionalStringField($address, $data, 'addressTag', 'setAddressTag');
        $this->setOptionalStringField($address, $data, 'idCardNo', 'setIdCardNo');

        if (isset($data['createdBy'])) {
            $createdBy = $data['createdBy'];
            if (is_string($createdBy)) {
                $address->setCreatedBy($createdBy);
            } elseif (is_int($createdBy)) {
                $address->setCreatedBy((string) $createdBy);
            }
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setOptionalStringField(
        DeliveryAddress $address,
        array $data,
        string $fieldName,
        string $setter,
    ): void {
        if (!isset($data[$fieldName])) {
            return;
        }

        $value = $data[$fieldName];
        $stringValue = is_string($value) ? $value : null;

        match ($setter) {
            'setReceiverPhone' => $address->setReceiverPhone($stringValue),
            'setTown' => $address->setTown($stringValue),
            'setPostCode' => $address->setPostCode($stringValue),
            'setAddressTag' => $address->setAddressTag($stringValue),
            'setIdCardNo' => $address->setIdCardNo($stringValue),
            default => throw new \InvalidArgumentException("Unknown setter: {$setter}"),
        };
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(DeliveryAddressRepository::class, $this->repository);
    }

    public function testFindByUserId(): void
    {
        $userId = 1001;
        $defaultAddress = $this->createDeliveryAddress(['isDefault' => true, 'createdBy' => $userId]);
        $normalAddress1 = $this->createDeliveryAddress(['createdBy' => $userId]);
        $normalAddress2 = $this->createDeliveryAddress(['createdBy' => $userId]);
        $this->createDeliveryAddress(['createdBy' => 1002]);

        $result = $this->repository->findByUserId($userId);

        $this->assertCount(3, $result);
        $this->assertSame($defaultAddress->getId(), $result[0]->getId());
    }

    public function testFindDefaultByUserId(): void
    {
        $userId = 1001;
        $defaultAddress = $this->createDeliveryAddress(['isDefault' => true, 'createdBy' => $userId]);
        $this->createDeliveryAddress(['isDefault' => false, 'createdBy' => $userId]);

        $result = $this->repository->findDefaultByUserId($userId);

        $this->assertNotNull($result);
        $this->assertSame($defaultAddress->getId(), $result->getId());
        $this->assertTrue($result->isDefault());
    }

    public function testFindDefaultByUserIdReturnsNullWhenNoDefault(): void
    {
        $userId = 1001;
        $this->createDeliveryAddress(['isDefault' => false, 'createdBy' => $userId]);

        $result = $this->repository->findDefaultByUserId($userId);
        $this->assertNull($result);
    }

    public function testFindByReceiverMobile(): void
    {
        $userId = 1001;
        $mobile = '13800138000';
        $address1 = $this->createDeliveryAddress(['receiverMobile' => $mobile, 'createdBy' => $userId]);
        $address2 = $this->createDeliveryAddress(['receiverMobile' => $mobile, 'createdBy' => $userId]);
        $this->createDeliveryAddress(['receiverMobile' => '13900139000', 'createdBy' => $userId]);
        $this->createDeliveryAddress(['receiverMobile' => $mobile, 'createdBy' => 1002]);

        $result = $this->repository->findByReceiverMobile($mobile, $userId);

        $this->assertCount(2, $result);
        $addressIds = array_map(fn ($addr) => $addr->getId(), $result);
        $this->assertContains($address1->getId(), $addressIds);
        $this->assertContains($address2->getId(), $addressIds);
    }

    public function testFindGlobalBuyAddresses(): void
    {
        $userId = 1001;
        $globalAddress1 = $this->createDeliveryAddress(['supportGlobalBuy' => true, 'createdBy' => $userId]);
        $globalAddress2 = $this->createDeliveryAddress(['supportGlobalBuy' => true, 'createdBy' => $userId]);
        $this->createDeliveryAddress(['supportGlobalBuy' => false, 'createdBy' => $userId]);
        $this->createDeliveryAddress(['supportGlobalBuy' => true, 'createdBy' => 1002]);

        $result = $this->repository->findGlobalBuyAddresses($userId);

        $this->assertCount(2, $result);
        $addressIds = array_map(fn ($addr) => $addr->getId(), $result);
        $this->assertContains($globalAddress1->getId(), $addressIds);
        $this->assertContains($globalAddress2->getId(), $addressIds);
    }

    public function testSaveShouldPersistDeliveryAddressWithFlush(): void
    {
        $address = new DeliveryAddress();
        $address->setAccount($this->testAccount);
        $address->setReceiverName('John Doe');
        $address->setReceiverMobile('13900139000');
        $address->setProvince('上海市');
        $address->setCity('上海市');
        $address->setCounty('浦东新区');
        $address->setDetailAddress('浦东南路1000号');
        $address->setIsDefault(true);
        $address->setSupportGlobalBuy(false);

        $this->repository->save($address, true);
        $this->assertGreaterThan(0, $address->getId());

        $persisted = $this->repository->find($address->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('John Doe', $persisted->getReceiverName());
        $this->assertSame('13900139000', $persisted->getReceiverMobile());
        $this->assertSame('上海市', $persisted->getProvince());
        $this->assertTrue($persisted->isDefault());
    }

    public function testSaveShouldPersistDeliveryAddressWithoutFlush(): void
    {
        $address = new DeliveryAddress();
        $address->setAccount($this->testAccount);
        $address->setReceiverName('Jane Smith');
        $address->setReceiverMobile('13700137000');
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setCounty('南山区');
        $address->setDetailAddress('科技园北区');
        $address->setIsDefault(false);
        $address->setSupportGlobalBuy(true);

        $this->repository->save($address, false);
        self::getEntityManager()->flush();
        $this->assertGreaterThan(0, $address->getId());

        $persisted = $this->repository->find($address->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('Jane Smith', $persisted->getReceiverName());
        $this->assertFalse($persisted->isDefault());
        $this->assertTrue($persisted->supportGlobalBuy());
    }

    public function testRemoveShouldDeleteDeliveryAddressWithFlush(): void
    {
        $address = $this->createDeliveryAddress(['receiverName' => 'Address to Delete']);
        $addressId = $address->getId();

        $this->repository->remove($address, true);

        $deleted = $this->repository->find($addressId);
        $this->assertNull($deleted);
    }

    public function testRemoveShouldDeleteDeliveryAddressWithoutFlush(): void
    {
        $address = $this->createDeliveryAddress(['receiverName' => 'Address to Delete No Flush']);
        $addressId = $address->getId();

        $this->repository->remove($address, false);
        self::getEntityManager()->flush();

        $deleted = $this->repository->find($addressId);
        $this->assertNull($deleted);
    }

    public function testFindShouldReturnDeliveryAddressById(): void
    {
        $address = $this->createDeliveryAddress(['receiverName' => 'Findable Address']);

        $found = $this->repository->find($address->getId());
        $this->assertNotNull($found);
        $this->assertSame($address->getId(), $found->getId());
        $this->assertSame('Findable Address', $found->getReceiverName());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $found = $this->repository->find(99999);
        $this->assertNull($found);
    }

    public function testFindAllShouldReturnAllDeliveryAddresses(): void
    {
        $initialCount = count($this->repository->findAll());

        $address1 = $this->createDeliveryAddress(['receiverName' => 'Address 1']);
        $address2 = $this->createDeliveryAddress(['receiverName' => 'Address 2']);

        $all = $this->repository->findAll();
        $this->assertCount($initialCount + 2, $all);

        $names = array_map(fn ($addr) => $addr->getReceiverName(), $all);
        $this->assertContains('Address 1', $names);
        $this->assertContains('Address 2', $names);
    }

    public function testFindByShouldReturnDeliveryAddressesMatchingCriteria(): void
    {
        $this->createDeliveryAddress(['receiverName' => 'Same Name', 'receiverMobile' => '13100131000']);
        $this->createDeliveryAddress(['receiverName' => 'Same Name', 'receiverMobile' => '13200132000']);
        $this->createDeliveryAddress(['receiverName' => 'Different Name', 'receiverMobile' => '13300133000']);

        $found = $this->repository->findBy(['receiverName' => 'Same Name']);
        $this->assertCount(2, $found);

        foreach ($found as $address) {
            $this->assertSame('Same Name', $address->getReceiverName());
        }
    }

    public function testFindOneByShouldReturnSingleDeliveryAddressMatchingCriteria(): void
    {
        $address = $this->createDeliveryAddress(['receiverMobile' => '13555135555']);
        $this->createDeliveryAddress(['receiverMobile' => '13666136666']);

        $found = $this->repository->findOneBy(['receiverMobile' => '13555135555']);
        $this->assertNotNull($found);
        $this->assertSame($address->getId(), $found->getId());
        $this->assertSame('13555135555', $found->getReceiverMobile());
    }

    public function testFindOneByShouldReturnNullWhenNoCriteriaMatch(): void
    {
        $this->createDeliveryAddress(['receiverMobile' => '13888138888']);

        $found = $this->repository->findOneBy(['receiverMobile' => '13999139999']);
        $this->assertNull($found);
    }

    public function testFindByUserIdShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createDeliveryAddress(['createdBy' => 1001]);

        $result = $this->repository->findByUserId(9999);
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindByReceiverMobileShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createDeliveryAddress(['receiverMobile' => '13111131111', 'createdBy' => 1001]);

        $result = $this->repository->findByReceiverMobile('13222132222', 1001);
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindGlobalBuyAddressesShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createDeliveryAddress(['supportGlobalBuy' => false, 'createdBy' => 1001]);

        $result = $this->repository->findGlobalBuyAddresses(1001);
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testSaveWithOptionalFieldsShouldPersistAllData(): void
    {
        $address = new DeliveryAddress();
        $address->setAccount($this->testAccount);
        $address->setReceiverName('Full Address');
        $address->setReceiverMobile('13444134444');
        $address->setReceiverPhone('010-12345678');
        $address->setProvince('江苏省');
        $address->setCity('南京市');
        $address->setCounty('鼓楼区');
        $address->setTown('某某街道');
        $address->setDetailAddress('某某路123号');
        $address->setPostCode('210000');
        $address->setAddressTag('公司');
        $address->setIdCardNo('320101199001011234');
        $address->setIsDefault(false);
        $address->setSupportGlobalBuy(true);
        $address->setCreatedBy('1001');

        $this->repository->save($address);

        $persisted = $this->repository->find($address->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('Full Address', $persisted->getReceiverName());
        $this->assertSame('010-12345678', $persisted->getReceiverPhone());
        $this->assertSame('某某街道', $persisted->getTown());
        $this->assertSame('210000', $persisted->getPostCode());
        $this->assertSame('公司', $persisted->getAddressTag());
        $this->assertSame('320101199001011234', $persisted->getIdCardNo());
        $this->assertSame('1001', $persisted->getCreatedBy());
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $oldAddress = $this->createDeliveryAddress(['isDefault' => true, 'receiverName' => 'AAA Old']);
        $newAddress = $this->createDeliveryAddress(['isDefault' => true, 'receiverName' => 'ZZZ New']);

        // 找到默认地址中按姓名排序最后的那个
        $result = $this->repository->findOneBy(['isDefault' => true], ['receiverName' => 'DESC']);

        $this->assertInstanceOf(DeliveryAddress::class, $result);
        $this->assertSame($newAddress->getId(), $result->getId());
    }

    public function testFindByWithNullCriteriaShouldFindAddressesWithNullValues(): void
    {
        $addressWithPhone = $this->createDeliveryAddress(['receiverPhone' => '010-12345678']);
        $addressWithoutPhone = $this->createDeliveryAddress(); // 没有设置 receiverPhone

        $result = $this->repository->findBy(['receiverPhone' => null]);

        $this->assertIsArray($result);
        // 验证结果中包含没有 receiverPhone 的地址
        $resultIds = array_map(fn ($address) => $address->getId(), $result);
        $this->assertContains($addressWithoutPhone->getId(), $resultIds);
        $this->assertNotContains($addressWithPhone->getId(), $resultIds);
    }

    public function testFindByWithBooleanCriteriaShouldReturnCorrectResults(): void
    {
        $defaultAddress = $this->createDeliveryAddress(['isDefault' => true, 'receiverName' => 'Default']);
        $normalAddress = $this->createDeliveryAddress(['isDefault' => false, 'receiverName' => 'Normal']);

        $defaultResults = $this->repository->findBy(['isDefault' => true]);
        $normalResults = $this->repository->findBy(['isDefault' => false]);

        // 验证默认地址结果
        $defaultIds = array_map(fn ($address) => $address->getId(), $defaultResults);
        $this->assertContains($defaultAddress->getId(), $defaultIds);

        // 验证非默认地址结果
        $normalIds = array_map(fn ($address) => $address->getId(), $normalResults);
        $this->assertContains($normalAddress->getId(), $normalIds);
    }

    public function testFindByWithGlobalBuyCriteriaShouldReturnCorrectResults(): void
    {
        $globalAddress = $this->createDeliveryAddress(['supportGlobalBuy' => true, 'receiverName' => 'Global']);
        $localAddress = $this->createDeliveryAddress(['supportGlobalBuy' => false, 'receiverName' => 'Local']);

        $globalResults = $this->repository->findBy(['supportGlobalBuy' => true]);
        $localResults = $this->repository->findBy(['supportGlobalBuy' => false]);

        // 验证支持全球购地址结果
        $globalIds = array_map(fn ($address) => $address->getId(), $globalResults);
        $this->assertContains($globalAddress->getId(), $globalIds);

        // 验证不支持全球购地址结果
        $localIds = array_map(fn ($address) => $address->getId(), $localResults);
        $this->assertContains($localAddress->getId(), $localIds);
    }

    public function testFindByWithStringFieldCriteriaShouldReturnCorrectResults(): void
    {
        $beijingAddress1 = $this->createDeliveryAddress(['city' => '北京市', 'receiverName' => 'Beijing 1']);
        $beijingAddress2 = $this->createDeliveryAddress(['city' => '北京市', 'receiverName' => 'Beijing 2']);
        $shanghaiAddress = $this->createDeliveryAddress(['city' => '上海市', 'receiverName' => 'Shanghai']);

        $beijingResults = $this->repository->findBy(['city' => '北京市']);

        $this->assertIsArray($beijingResults);
        $this->assertGreaterThanOrEqual(2, count($beijingResults));

        $beijingIds = array_map(fn ($address) => $address->getId(), $beijingResults);
        $this->assertContains($beijingAddress1->getId(), $beijingIds);
        $this->assertContains($beijingAddress2->getId(), $beijingIds);
        $this->assertNotContains($shanghaiAddress->getId(), $beijingIds);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key');
        $otherAccount->setAppSecret('other-app-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createDeliveryAddress(['receiverName' => 'Test Account Address']);

        $addressWithOtherAccount = new DeliveryAddress();
        $addressWithOtherAccount->setAccount($otherAccount);
        $addressWithOtherAccount->setReceiverName('Other Account Address');
        $addressWithOtherAccount->setReceiverMobile('13800138001');
        $addressWithOtherAccount->setProvince('上海市');
        $addressWithOtherAccount->setCity('上海市');
        $addressWithOtherAccount->setCounty('浦东新区');
        $addressWithOtherAccount->setDetailAddress('测试地址');
        $addressWithOtherAccount->setIsDefault(false);
        $addressWithOtherAccount->setSupportGlobalBuy(false);
        $this->persistAndFlush($addressWithOtherAccount);

        $result = $this->repository->findOneBy(['account' => $this->testAccount]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(DeliveryAddress::class, $result);
        $this->assertSame($this->testAccount->getId(), $result->getAccount()->getId());
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key-2');
        $otherAccount->setAppSecret('other-app-secret-2');
        $otherAccount->setName('Other Account 2');
        $this->persistAndFlush($otherAccount);

        $initialCount = $this->repository->count(['account' => $this->testAccount]);

        $this->createDeliveryAddress(['receiverName' => 'Test Account 1']);
        $this->createDeliveryAddress(['receiverName' => 'Test Account 2']);

        $addressWithOtherAccount = new DeliveryAddress();
        $addressWithOtherAccount->setAccount($otherAccount);
        $addressWithOtherAccount->setReceiverName('Other Account Address');
        $addressWithOtherAccount->setReceiverMobile('13800138002');
        $addressWithOtherAccount->setProvince('广州市');
        $addressWithOtherAccount->setCity('广州市');
        $addressWithOtherAccount->setCounty('天河区');
        $addressWithOtherAccount->setDetailAddress('其他测试地址');
        $addressWithOtherAccount->setIsDefault(false);
        $addressWithOtherAccount->setSupportGlobalBuy(false);
        $this->persistAndFlush($addressWithOtherAccount);

        $count = $this->repository->count(['account' => $this->testAccount]);
        $this->assertSame($initialCount + 2, $count);
    }

    protected function getRepository(): DeliveryAddressRepository
    {
        return self::getService(DeliveryAddressRepository::class);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setAppKey('test-app-key-' . uniqid());
        $account->setAppSecret('test-app-secret-' . uniqid());
        $account->setName('Test Account ' . uniqid());

        $deliveryAddress = new DeliveryAddress();
        $deliveryAddress->setAccount($account);
        $deliveryAddress->setReceiverName('张三');
        $deliveryAddress->setReceiverMobile('13800138000');
        $deliveryAddress->setProvince('北京市');
        $deliveryAddress->setCity('北京市');
        $deliveryAddress->setCounty('朝阳区');
        $deliveryAddress->setDetailAddress('朝阳区三里屯太古里');

        return $deliveryAddress;
    }
}
