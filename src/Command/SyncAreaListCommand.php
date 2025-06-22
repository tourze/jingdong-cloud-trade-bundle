<?php

namespace JingdongCloudTradeBundle\Command;

use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Service\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;

/**
 * @see https://jos.jd.com/apilistnewdetail?apiGroupId=415&apiId=18573&apiName=null
 */
#[AsCommand(name: self::NAME, description: '同步Area信息')]
class SyncAreaListCommand extends LockableCommand
{
    public const NAME = 'jingdong-pop:sync-area:list';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Client $client,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 地址是通用的，我们拿一个来读取即可
        $response = $this->client->execute(
            $this->accountRepository->findOneValid(),
            'jingdong.ldop.receive.trace.get',
            [
                'waybillCode' => "JDV019645978415",
                'customerCode' => '010K398090'
            ],
        );
        var_dump($response);

        return Command::SUCCESS;
    }
}
