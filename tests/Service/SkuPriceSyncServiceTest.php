<?php

namespace JingdongCloudTradeBundle\Tests\Service;

use JingdongCloudTradeBundle\Service\SkuPriceSyncService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SkuPriceSyncService::class)]
#[RunTestsInSeparateProcesses]
final class SkuPriceSyncServiceTest extends AbstractIntegrationTestCase
{
    private SkuPriceSyncService $service;

    private BufferedOutput $output;

    private SymfonyStyle $io;

    protected function onSetUp(): void
    {
        $this->output = new BufferedOutput();
        $this->io = new SymfonyStyle(new ArrayInput([]), $this->output);
        $this->service = self::getService(SkuPriceSyncService::class);
    }

    public function testSyncSkuPricesWithEmptyAccounts(): void
    {
        $this->service->syncSkuPrices($this->io, [], 100, false);

        $buffer = $this->output->fetch();
        $this->assertStringContainsString('正在同步商品价格', $buffer);
    }

    public function testServiceInstantiation(): void
    {
        $this->assertInstanceOf(SkuPriceSyncService::class, $this->service);
    }
}
