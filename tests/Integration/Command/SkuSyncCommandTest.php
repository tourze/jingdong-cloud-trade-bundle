<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Command;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Command\SkuSyncCommand;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Repository\SkuRepository;
use JingdongCloudTradeBundle\Service\Client;
use JingdongCloudTradeBundle\Service\SkuService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SkuSyncCommandTest extends TestCase
{
    private Client $client;
    private EntityManagerInterface $entityManager;
    private SkuRepository $skuRepository;
    private SkuService $skuService;
    private AccountRepository $accountRepository;
    private SkuSyncCommand $command;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->skuRepository = $this->createMock(SkuRepository::class);
        $this->skuService = $this->createMock(SkuService::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        
        $this->command = new SkuSyncCommand(
            $this->client,
            $this->entityManager,
            $this->skuRepository,
            $this->skuService,
            $this->accountRepository
        );
    }

    public function testGetName(): void
    {
        $this->assertEquals('jingdong:sku:sync', $this->command->getName());
    }

    public function testExecute_noAccounts(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 配置输入参数
        $input->method('getOption')->willReturnMap([
            ['account-id', null],
            ['category-id', null],
            ['brand-id', null],
            ['force', false],
            ['limit', 500],
            ['detail', false],
            ['price', false],
            ['stock', false],
        ]);

        $this->accountRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
    }

    public function testExecute_withAccountId(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        
        // 使用反射设置 ID
        $reflection = new \ReflectionClass($account);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($account, 123);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 配置输入参数
        $input->method('getOption')->willReturnMap([
            ['account-id', '123'],
            ['category-id', null],
            ['brand-id', null],
            ['force', false],
            ['limit', 500],
            ['detail', false],
            ['price', false],
            ['stock', false],
        ]);

        $this->accountRepository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($account);

        // 模拟API返回空数据
        $this->client->expects($this->once())
            ->method('execute')
            ->willReturn([
                'result' => [
                    'success' => true,
                    'materialSkuVoList' => [],
                    'total' => 0,
                ]
            ]);

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function testExecute_accountNotFound(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 配置输入参数
        $input->method('getOption')->willReturnMap([
            ['account-id', '999'],
            ['category-id', null],
            ['brand-id', null],
            ['force', false],
            ['limit', 500],
            ['detail', false],
            ['price', false],
            ['stock', false],
        ]);

        $this->accountRepository->expects($this->once())
            ->method('find')
            ->with('999')
            ->willReturn(null);

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
    }
}