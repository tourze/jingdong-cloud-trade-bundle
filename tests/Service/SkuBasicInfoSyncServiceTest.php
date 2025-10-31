<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Service\SkuBasicInfoSyncService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SkuBasicInfoSyncService::class)]
#[RunTestsInSeparateProcesses]
final class SkuBasicInfoSyncServiceTest extends AbstractIntegrationTestCase
{
    private SkuBasicInfoSyncService $service;

    private BufferedOutput $output;

    private SymfonyStyle $io;

    protected function onSetUp(): void
    {
        $this->output = new BufferedOutput();
        $this->io = new SymfonyStyle(new ArrayInput([]), $this->output);
        $this->service = self::getService(SkuBasicInfoSyncService::class);
    }

    public function testSyncBasicSkuInfoWithEmptyAccounts(): void
    {
        $result = $this->service->syncBasicSkuInfo($this->io, [], []);

        $buffer = $this->output->fetch();
        $this->assertStringContainsString('正在同步商品基本信息', $buffer);
        $this->assertSame(0, $result);
    }

    public function testServiceInstantiation(): void
    {
        $this->assertInstanceOf(SkuBasicInfoSyncService::class, $this->service);
    }
}
