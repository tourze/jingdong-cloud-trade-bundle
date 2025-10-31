<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Service\SkuDetailSyncService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SkuDetailSyncService::class)]
#[RunTestsInSeparateProcesses]
final class SkuDetailSyncServiceTest extends AbstractIntegrationTestCase
{
    private SkuDetailSyncService $service;

    private BufferedOutput $output;

    private SymfonyStyle $io;

    protected function onSetUp(): void
    {
        $this->output = new BufferedOutput();
        $this->io = new SymfonyStyle(new ArrayInput([]), $this->output);
        $this->service = self::getService(SkuDetailSyncService::class);
    }

    public function testSyncSkuDetailsWithEmptyAccounts(): void
    {
        $this->service->syncSkuDetails($this->io, [], 100, false);

        $buffer = $this->output->fetch();
        $this->assertStringContainsString('正在同步商品详情', $buffer);
    }

    public function testServiceInstantiation(): void
    {
        $this->assertInstanceOf(SkuDetailSyncService::class, $this->service);
    }
}
