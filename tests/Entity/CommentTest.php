<?php

namespace JingdongCloudTradeBundle\Tests\Entity;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Comment;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\OrderItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Comment::class)]
final class CommentTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        // 创建关联实体
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppKey('test_app_key');
        $account->setAppSecret('test_app_secret');

        $order = new Order();
        $order->setOrderId('JD123456789');
        $order->setOrderState('PROCESSING');
        $order->setPaymentState('PAID');
        $order->setLogisticsState('SHIPPED');
        $order->setAccount($account);

        $orderItem = new OrderItem();
        $orderItem->setSkuId('SKU123456');
        $orderItem->setSkuName('Test Product');
        $orderItem->setOrder($order);
        $orderItem->setAccount($account);

        $comment = new Comment();
        $comment->setAccount($account);
        $comment->setOrder($order);
        $comment->setOrderItem($orderItem);
        $comment->setScore('5');
        $comment->setCommentTime(new \DateTimeImmutable());

        return $comment;
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'score' => ['score', '5'];
        yield 'content' => ['content', 'This is a test comment'];
        yield 'images' => ['images', ['https://example.com/image1.jpg', 'https://example.com/image2.jpg']];
        yield 'commentTime' => ['commentTime', new \DateTimeImmutable()];
        yield 'approveTime' => ['approveTime', new \DateTimeImmutable()];
    }
}
