<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Command;

use JingdongCloudTradeBundle\Command\SyncAreaListCommand;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Service\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncAreaListCommandTest extends TestCase
{
    private AccountRepository $accountRepository;
    private Client $client;
    private SyncAreaListCommand $command;

    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->client = $this->createMock(Client::class);
        
        $this->command = new SyncAreaListCommand(
            $this->accountRepository,
            $this->client
        );
    }

    public function testGetName(): void
    {
        $this->assertEquals('jingdong-pop:sync-area:list', $this->command->getName());
    }

    public function testExecute_success(): void
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

        $this->accountRepository->expects($this->once())
            ->method('findOneValid')
            ->willReturn($account);

        $this->client->expects($this->once())
            ->method('execute')
            ->with(
                $account,
                'jingdong.ldop.receive.trace.get',
                [
                    'waybillCode' => "JDV019645978415",
                    'customerCode' => '010K398090'
                ]
            )
            ->willReturn([
                'result' => [
                    'success' => true,
                    'data' => []
                ]
            ]);

        $result = $this->command->run($input, $output);

        $this->assertEquals(Command::SUCCESS, $result);
    }
}