<?php

namespace JingdongCloudTradeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Comment;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\OrderItem;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public const COMMENT_REFERENCE = 'comment';

    public function load(ObjectManager $manager): void
    {
        $comment = new Comment();
        $comment->setAccount($this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class));
        $comment->setOrder($this->getReference(OrderFixtures::ORDER_REFERENCE, Order::class));
        $comment->setOrderItem($this->getReference(OrderItemFixtures::ORDER_ITEM_REFERENCE, OrderItem::class));
        $comment->setScore('5');
        $comment->setContent('测试评论内容');
        $comment->setCommentTime(new \DateTimeImmutable());

        $manager->persist($comment);
        $manager->flush();

        $this->addReference(self::COMMENT_REFERENCE, $comment);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            OrderFixtures::class,
            OrderItemFixtures::class,
        ];
    }
}
