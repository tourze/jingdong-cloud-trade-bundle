<?php

namespace JingdongCloudTradeBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Command\SkuSyncCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SkuSyncCommand::class)]
#[RunTestsInSeparateProcesses]
final class SkuSyncCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        /** @var SkuSyncCommand $command */
        $command = self::getContainer()->get(SkuSyncCommand::class);

        $application = new Application();
        $application->add($command);

        $command = $application->find('jingdong:sku:sync');
        $this->commandTester = new CommandTester($command);
    }

    public function testGetName(): void
    {
        /** @var SkuSyncCommand $command */
        $command = self::getContainer()->get(SkuSyncCommand::class);
        $this->assertEquals('jingdong:sku:sync', $command->getName());
    }

    public function testExecuteNoAccounts(): void
    {
        // 清空账户数据以测试没有账户的情况
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->createQuery('DELETE FROM JingdongCloudTradeBundle\Entity\Account')->execute();

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecuteAccountNotFound(): void
    {
        $this->commandTester->execute(['--account-id' => '999']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testOptionAccountId(): void
    {
        $this->commandTester->execute(['--account-id' => '1']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testOptionCategoryId(): void
    {
        $this->commandTester->execute(['--category-id' => '1']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testOptionBrandId(): void
    {
        $this->commandTester->execute(['--brand-id' => '1']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testOptionForce(): void
    {
        $this->commandTester->execute(['--force']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testOptionLimit(): void
    {
        $this->commandTester->execute(['--limit' => '100']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testOptionDetail(): void
    {
        $this->commandTester->execute(['--detail']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testOptionPrice(): void
    {
        $this->commandTester->execute(['--price']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testOptionStock(): void
    {
        $this->commandTester->execute(['--stock']);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
