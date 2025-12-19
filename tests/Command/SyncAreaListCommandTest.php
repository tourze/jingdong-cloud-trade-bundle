<?php

namespace JingdongCloudTradeBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Command\SyncAreaListCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncAreaListCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncAreaListCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        /** @var SyncAreaListCommand $command */
        $command = self::getContainer()->get(SyncAreaListCommand::class);

        $application = new Application();
        $application->addCommand($command);

        $command = $application->find('jingdong-pop:sync-area:list');
        $this->commandTester = new CommandTester($command);
    }

    public function testGetName(): void
    {
        /** @var SyncAreaListCommand $command */
        $command = self::getContainer()->get(SyncAreaListCommand::class);
        $this->assertEquals('jingdong-pop:sync-area:list', $command->getName());
    }

    public function testExecuteWithoutValidAccount(): void
    {
        // 清空账户数据以测试没有有效账户的情况
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->createQuery('DELETE FROM JingdongCloudTradeBundle\Entity\Account')->execute();

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
