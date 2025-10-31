<?php

declare(strict_types=1);

namespace JingdongCloudTradeBundle\Tests;

use JingdongCloudTradeBundle\JingdongCloudTradeBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(JingdongCloudTradeBundle::class)]
#[RunTestsInSeparateProcesses]
final class JingdongCloudTradeBundleTest extends AbstractBundleTestCase
{
}
