<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Service\SkuStockSyncService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SkuStockSyncService::class)]
#[RunTestsInSeparateProcesses]
final class SkuStockSyncServiceTest extends AbstractIntegrationTestCase
{
    private SkuStockSyncService $service;

    private BufferedOutput $output;

    private SymfonyStyle $io;

    protected function onSetUp(): void
    {
        $this->output = new BufferedOutput();
        $this->io = new SymfonyStyle(new ArrayInput([]), $this->output);
        $this->service = self::getService(SkuStockSyncService::class);
    }

    public function testSyncSkuStocksWithEmptyAccounts(): void
    {
        $this->service->syncSkuStocks($this->io, [], 100, false);

        $buffer = $this->output->fetch();
        $this->assertStringContainsString('正在同步商品库存', $buffer);
    }

    public function testServiceInstantiation(): void
    {
        $this->assertInstanceOf(SkuStockSyncService::class, $this->service);
    }
}
