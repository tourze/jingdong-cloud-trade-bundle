<?php

namespace JingdongCloudTradeBundle\Command;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Service\SkuBasicInfoSyncService;
use JingdongCloudTradeBundle\Service\SkuDetailSyncService;
use JingdongCloudTradeBundle\Service\SkuPriceSyncService;
use JingdongCloudTradeBundle\Service\SkuStockSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 同步京东商品数据命令
 */
#[AsCommand(
    name: self::NAME,
    description: '同步京东商品数据'
)]
final class SkuSyncCommand extends Command
{
    public const NAME = 'jingdong:sku:sync';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly SkuBasicInfoSyncService $basicInfoSyncService,
        private readonly SkuDetailSyncService $detailSyncService,
        private readonly SkuPriceSyncService $priceSyncService,
        private readonly SkuStockSyncService $stockSyncService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('account-id', 'a', InputOption::VALUE_OPTIONAL, '指定同步的京东账户ID')
            ->addOption('category-id', 'c', InputOption::VALUE_OPTIONAL, '指定同步的商品分类ID')
            ->addOption('brand-id', 'b', InputOption::VALUE_OPTIONAL, '指定同步的品牌ID')
            ->addOption('force', 'f', InputOption::VALUE_NONE, '强制重新同步所有商品')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, '同步商品数量限制', 500)
            ->addOption('detail', 'd', InputOption::VALUE_NONE, '是否同步商品详情')
            ->addOption('price', 'p', InputOption::VALUE_NONE, '是否同步商品价格')
            ->addOption('stock', 's', InputOption::VALUE_NONE, '是否同步商品库存')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('开始同步京东商品数据');

        $options = $this->parseOptions($input);
        $accountId = $options['accountId'];
        assert(is_string($accountId) || null === $accountId);
        $accounts = $this->getTargetAccounts($io, $accountId);

        if ([] === $accounts) {
            return Command::FAILURE;
        }

        try {
            $totalSynced = $this->basicInfoSyncService->syncBasicSkuInfo($io, $accounts, $options);
            $this->syncAdditionalData($io, $accounts, $options);
        } catch (\Throwable $e) {
            $io->error('同步过程中发生错误: ' . $e->getMessage());

            return Command::FAILURE;
        }

        if (0 === $totalSynced) {
            $io->warning('未同步任何商品数据');

            return Command::FAILURE;
        }

        $io->success("商品数据同步完成，共同步 {$totalSynced} 个商品");

        return Command::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function parseOptions(InputInterface $input): array
    {
        $accountId = $input->getOption('account-id');
        $categoryId = $input->getOption('category-id');
        $brandId = $input->getOption('brand-id');
        $force = $input->getOption('force');
        $limit = $input->getOption('limit');
        $syncDetail = $input->getOption('detail');
        $syncPrice = $input->getOption('price');
        $syncStock = $input->getOption('stock');

        return [
            'accountId' => is_string($accountId) ? $accountId : null,
            'categoryId' => is_string($categoryId) ? $categoryId : null,
            'brandId' => is_string($brandId) ? $brandId : null,
            'force' => is_bool($force) ? $force : false,
            'limit' => is_numeric($limit) ? (int) $limit : 500,
            'syncDetail' => is_bool($syncDetail) ? $syncDetail : false,
            'syncPrice' => is_bool($syncPrice) ? $syncPrice : false,
            'syncStock' => is_bool($syncStock) ? $syncStock : false,
        ];
    }

    /**
     * @return Account[]
     */
    private function getTargetAccounts(SymfonyStyle $io, ?string $accountId): array
    {
        if (null !== $accountId) {
            $account = $this->accountRepository->find($accountId);
            if (null === $account) {
                $io->error("账户ID: {$accountId} 不存在");

                return [];
            }

            return [$account];
        }

        $accounts = $this->accountRepository->findAll();
        if ([] === $accounts) {
            $io->error('系统中没有配置京东账户');

            return [];
        }

        return $accounts;
    }

    /**
     * @param Account[] $accounts
     * @param array<string, mixed> $options
     */
    private function syncAdditionalData(SymfonyStyle $io, array $accounts, array $options): void
    {
        if (true === $options['syncDetail']) {
            $limit = $options['limit'];
            $force = $options['force'];
            assert(is_int($limit));
            assert(is_bool($force));
            $this->detailSyncService->syncSkuDetails($io, $accounts, $limit, $force);
        }

        if (true === $options['syncPrice']) {
            $limit = $options['limit'];
            $force = $options['force'];
            assert(is_int($limit));
            assert(is_bool($force));
            $this->priceSyncService->syncSkuPrices($io, $accounts, $limit, $force);
        }

        if (true === $options['syncStock']) {
            $limit = $options['limit'];
            $force = $options['force'];
            assert(is_int($limit));
            assert(is_bool($force));
            $this->stockSyncService->syncSkuStocks($io, $accounts, $limit, $force);
        }
    }
}
