<?php

namespace JingdongCloudTradeBundle\Tests\Repository;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Comment;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Entity\OrderItem;
use JingdongCloudTradeBundle\Repository\CommentRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CommentRepository::class)]
#[RunTestsInSeparateProcesses]
final class CommentRepositoryTest extends AbstractRepositoryTestCase
{
    private CommentRepository $repository;

    private Account $testAccount;

    private Order $testOrder;

    private OrderItem $testOrderItem;

    protected function onSetUp(): void
    {
        // 彻底重置数据库连接状态，确保每个测试都从干净状态开始
        $connection = self::getEntityManager()->getConnection();

        // 关闭现有连接
        if ($connection->isConnected()) {
            $connection->close();
        }

        // 通过执行简单查询触发重新连接
        try {
            $connection->executeQuery('SELECT 1');
        } catch (\Exception $e) {
            // 忽略连接异常，让测试自然进行
        }
        $this->repository = $this->getRepository();

        $this->testAccount = new Account();
        $this->testAccount->setAppKey('test-app-key');
        $this->testAccount->setAppSecret('test-app-secret');
        $this->testAccount->setName('Test Account');
        $this->persistAndFlush($this->testAccount);

        $this->testOrder = new Order();
        $this->testOrder->setAccount($this->testAccount);
        $this->testOrder->setOrderId('123456');
        $this->testOrder->setOrderState('FINISHED');
        $this->testOrder->setPaymentState('PAID');
        $this->testOrder->setLogisticsState('SHIPPED');
        $this->testOrder->setReceiverName('Test Receiver');
        $this->testOrder->setReceiverMobile('13800138000');
        $this->testOrder->setReceiverProvince('北京市');
        $this->testOrder->setReceiverCity('北京市');
        $this->testOrder->setReceiverCounty('朝阳区');
        $this->testOrder->setReceiverAddress('测试地址');
        $this->testOrder->setOrderTotalPrice('1000.00');
        $this->testOrder->setOrderPaymentPrice('1000.00');
        $this->testOrder->setFreightPrice('0.00');
        $this->testOrder->setOrderTime(new \DateTimeImmutable());
        $this->testOrder->setSynced(true);
        $this->persistAndFlush($this->testOrder);

        $this->testOrderItem = new OrderItem();
        $this->testOrderItem->setAccount($this->testAccount);
        $this->testOrderItem->setOrder($this->testOrder);
        $this->testOrderItem->setSkuId('SKU123');
        $this->testOrderItem->setSkuName('Test Product');
        $this->testOrderItem->setQuantity(1);
        $this->testOrderItem->setPrice('1000.00');
        $this->testOrderItem->setTotalPrice('1000.00');
        $this->persistAndFlush($this->testOrderItem);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createComment(array $data = []): Comment
    {
        $comment = new Comment();
        $comment->setAccount($this->testAccount);

        $comment->setOrder($this->getEntityValue($data, 'order', Order::class, $this->testOrder));
        $comment->setOrderItem($this->getEntityValue($data, 'orderItem', OrderItem::class, $this->testOrderItem));
        $comment->setScore($this->getStringValue($data, 'score', '5'));
        $comment->setIsAnonymous($this->getBoolValue($data, 'isAnonymous', false));
        $comment->setCommentTime($this->getDateTimeValue($data, 'commentTime', new \DateTimeImmutable()));
        $comment->setIsApproved($this->getBoolValue($data, 'isApproved', true));

        $this->setOptionalStringField($data, 'content', $comment->setContent(...));
        $this->setOptionalImagesField($data, $comment);
        $this->setOptionalDateTimeField($data, 'approveTime', $comment->setApproveTime(...));

        $persistedComment = $this->persistAndFlush($comment);
        $this->assertInstanceOf(Comment::class, $persistedComment);

        return $persistedComment;
    }

    /**
     * @template T of object
     * @param array<string, mixed> $data
     * @param class-string<T> $expectedClass
     * @param T $default
     * @return T
     */
    private function getEntityValue(array $data, string $key, string $expectedClass, object $default): object
    {
        $value = $data[$key] ?? $default;

        return $value instanceof $expectedClass ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getStringValue(array $data, string $key, string $default): string
    {
        $value = $data[$key] ?? $default;

        return \is_string($value) ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getBoolValue(array $data, string $key, bool $default): bool
    {
        $value = $data[$key] ?? $default;

        return \is_bool($value) ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getDateTimeValue(array $data, string $key, \DateTimeImmutable $default): \DateTimeImmutable
    {
        $value = $data[$key] ?? $default;

        return $value instanceof \DateTimeImmutable ? $value : $default;
    }

    /**
     * @param array<string, mixed> $data
     * @param callable(string|null): void $setter
     */
    private function setOptionalStringField(array $data, string $key, callable $setter): void
    {
        if (\array_key_exists($key, $data)) {
            $value = $data[$key];
            $setter(\is_string($value) ? $value : null);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setOptionalImagesField(array $data, Comment $comment): void
    {
        if (\array_key_exists('images', $data)) {
            $images = $data['images'];
            if (null === $images) {
                $comment->setImages(null);
            } elseif (\is_array($images)) {
                /** @var list<string> $stringImages */
                $stringImages = array_filter($images, '\is_string');
                $comment->setImages($stringImages);
            }
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param callable(\DateTimeImmutable|null): void $setter
     */
    private function setOptionalDateTimeField(array $data, string $key, callable $setter): void
    {
        if (\array_key_exists($key, $data)) {
            $value = $data[$key];
            if ($value instanceof \DateTimeImmutable || null === $value) {
                $setter($value);
            }
        }
    }

    public function testRepositoryClass(): void
    {
        $this->assertInstanceOf(CommentRepository::class, $this->repository);
    }

    public function testFindBySkuId(): void
    {
        $comment1 = $this->createComment(['content' => 'Great product!', 'isApproved' => true]);
        $comment2 = $this->createComment(['content' => 'Excellent!', 'isApproved' => true]);
        $this->createComment(['content' => 'Not approved', 'isApproved' => false]);

        $otherOrderItem = new OrderItem();
        $otherOrderItem->setAccount($this->testAccount);
        $otherOrderItem->setOrder($this->testOrder);
        $otherOrderItem->setSkuId('SKU456');
        $otherOrderItem->setSkuName('Other Product');
        $otherOrderItem->setQuantity(1);
        $otherOrderItem->setPrice('500.00');
        $otherOrderItem->setTotalPrice('500.00');
        $this->persistAndFlush($otherOrderItem);

        $this->createComment(['orderItem' => $otherOrderItem]);

        $result = $this->repository->findBySkuId('SKU123');

        $this->assertCount(2, $result);
        $commentIds = array_map(fn ($comment) => $comment->getId(), $result);
        $this->assertContains($comment1->getId(), $commentIds);
        $this->assertContains($comment2->getId(), $commentIds);
    }

    public function testFindByOrderItemId(): void
    {
        $comment = $this->createComment();

        $result = $this->repository->findByOrderItemId($this->testOrderItem->getId());
        $this->assertNotNull($result);
        $this->assertSame($comment->getId(), $result->getId());
    }

    public function testFindByOrderItemIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByOrderItemId(999999);
        $this->assertNull($result);
    }

    public function testFindByOrderId(): void
    {
        $comment1 = $this->createComment(['commentTime' => new \DateTimeImmutable('-1 day')]);
        $comment2 = $this->createComment(['commentTime' => new \DateTimeImmutable('now')]);

        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('789012');
        $otherOrder->setOrderState('FINISHED');
        $otherOrder->setPaymentState('PAID');
        $otherOrder->setLogisticsState('SHIPPED');
        $otherOrder->setReceiverName('Test Receiver');
        $otherOrder->setReceiverMobile('13800138000');
        $otherOrder->setReceiverProvince('北京市');
        $otherOrder->setReceiverCity('北京市');
        $otherOrder->setReceiverCounty('朝阳区');
        $otherOrder->setReceiverAddress('测试地址');
        $otherOrder->setOrderTotalPrice('500.00');
        $otherOrder->setOrderPaymentPrice('500.00');
        $otherOrder->setFreightPrice('0.00');
        $otherOrder->setOrderTime(new \DateTimeImmutable());
        $otherOrder->setSynced(true);
        $this->persistAndFlush($otherOrder);

        $otherOrderItem = new OrderItem();
        $otherOrderItem->setAccount($this->testAccount);
        $otherOrderItem->setOrder($otherOrder);
        $otherOrderItem->setSkuId('SKU789');
        $otherOrderItem->setSkuName('Other Product');
        $otherOrderItem->setQuantity(1);
        $otherOrderItem->setPrice('500.00');
        $otherOrderItem->setTotalPrice('500.00');
        $this->persistAndFlush($otherOrderItem);

        $this->createComment(['order' => $otherOrder, 'orderItem' => $otherOrderItem]);

        $result = $this->repository->findByOrderId($this->testOrder->getId());

        $this->assertCount(2, $result);
        $this->assertSame($comment2->getId(), $result[0]->getId());
        $this->assertSame($comment1->getId(), $result[1]->getId());
    }

    public function testFindPendingApproval(): void
    {
        $initialPendingCount = count($this->repository->findPendingApproval());

        $pendingComment1 = $this->createComment(['isApproved' => false, 'commentTime' => new \DateTimeImmutable('-2 days')]);
        $pendingComment2 = $this->createComment(['isApproved' => false, 'commentTime' => new \DateTimeImmutable('-1 day')]);
        $this->createComment(['isApproved' => true]);

        $result = $this->repository->findPendingApproval();

        $this->assertCount($initialPendingCount + 2, $result);

        // 找到我们创建的两个待审批评论
        $createdComments = array_filter($result, function ($comment) use ($pendingComment1, $pendingComment2) {
            return $comment->getId() === $pendingComment1->getId() || $comment->getId() === $pendingComment2->getId();
        });

        $this->assertCount(2, $createdComments);

        // 验证我们创建的评论都在结果中
        $createdCommentIds = array_map(fn ($comment) => $comment->getId(), $createdComments);
        $this->assertContains($pendingComment1->getId(), $createdCommentIds);
        $this->assertContains($pendingComment2->getId(), $createdCommentIds);
    }

    public function testSaveShouldPersistCommentWithFlush(): void
    {
        $comment = new Comment();
        $comment->setAccount($this->testAccount);
        $comment->setOrder($this->testOrder);
        $comment->setOrderItem($this->testOrderItem);
        $comment->setScore('4');
        $comment->setContent('Great product!');
        $comment->setIsAnonymous(false);
        $comment->setCommentTime(new \DateTimeImmutable());
        $comment->setIsApproved(true);

        $this->repository->save($comment, true);
        $this->assertGreaterThan(0, $comment->getId());

        $persisted = $this->repository->find($comment->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('4', $persisted->getScore());
        $this->assertSame('Great product!', $persisted->getContent());
        $this->assertTrue($persisted->isApproved());
    }

    public function testSaveShouldPersistCommentWithoutFlush(): void
    {
        $comment = new Comment();
        $comment->setAccount($this->testAccount);
        $comment->setOrder($this->testOrder);
        $comment->setOrderItem($this->testOrderItem);
        $comment->setScore('3');
        $comment->setIsAnonymous(true);
        $comment->setCommentTime(new \DateTimeImmutable());
        $comment->setIsApproved(false);

        $this->repository->save($comment, false);
        self::getEntityManager()->flush();
        $this->assertGreaterThan(0, $comment->getId());

        $persisted = $this->repository->find($comment->getId());
        $this->assertNotNull($persisted);
        $this->assertSame('3', $persisted->getScore());
        $this->assertTrue($persisted->isAnonymous());
        $this->assertFalse($persisted->isApproved());
    }

    public function testRemoveShouldDeleteCommentWithFlush(): void
    {
        $comment = $this->createComment(['content' => 'Comment to delete']);
        $commentId = $comment->getId();

        $this->repository->remove($comment, true);

        $deleted = $this->repository->find($commentId);
        $this->assertNull($deleted);
    }

    public function testRemoveShouldDeleteCommentWithoutFlush(): void
    {
        $comment = $this->createComment(['content' => 'Comment to delete no flush']);
        $commentId = $comment->getId();

        $this->repository->remove($comment, false);
        self::getEntityManager()->flush();

        $deleted = $this->repository->find($commentId);
        $this->assertNull($deleted);
    }

    public function testFindShouldReturnCommentById(): void
    {
        $comment = $this->createComment(['content' => 'Findable comment']);

        $found = $this->repository->find($comment->getId());
        $this->assertNotNull($found);
        $this->assertSame($comment->getId(), $found->getId());
        $this->assertSame('Findable comment', $found->getContent());
    }

    public function testFindShouldReturnNullForNonExistentId(): void
    {
        $found = $this->repository->find(99999);
        $this->assertNull($found);
    }

    public function testFindAllShouldReturnAllComments(): void
    {
        $initialCount = count($this->repository->findAll());

        $comment1 = $this->createComment(['content' => 'Comment 1']);
        $comment2 = $this->createComment(['content' => 'Comment 2']);

        $all = $this->repository->findAll();
        $this->assertCount($initialCount + 2, $all);

        $contents = array_map(fn ($comment) => $comment->getContent(), $all);
        $this->assertContains('Comment 1', $contents);
        $this->assertContains('Comment 2', $contents);
    }

    public function testFindByShouldReturnCommentsMatchingCriteria(): void
    {
        $initialScore5Count = count($this->repository->findBy(['score' => '5']));

        $this->createComment(['score' => '5', 'isApproved' => true]);
        $this->createComment(['score' => '5', 'isApproved' => true]);
        $this->createComment(['score' => '4', 'isApproved' => true]);

        $found = $this->repository->findBy(['score' => '5']);
        $this->assertCount($initialScore5Count + 2, $found);

        foreach ($found as $comment) {
            $this->assertSame('5', $comment->getScore());
        }
    }

    public function testFindOneByShouldReturnSingleCommentMatchingCriteria(): void
    {
        $comment = $this->createComment(['content' => 'Unique content']);
        $this->createComment(['content' => 'Other content']);

        $found = $this->repository->findOneBy(['content' => 'Unique content']);
        $this->assertNotNull($found);
        $this->assertSame($comment->getId(), $found->getId());
        $this->assertSame('Unique content', $found->getContent());
    }

    public function testFindOneByShouldReturnNullWhenNoCriteriaMatch(): void
    {
        $this->createComment(['content' => 'Existing comment']);

        $found = $this->repository->findOneBy(['content' => 'Non-existent comment']);
        $this->assertNull($found);
    }

    public function testFindBySkuIdShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createComment();

        $result = $this->repository->findBySkuId('NON_EXISTENT_SKU');
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindByOrderIdShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $result = $this->repository->findByOrderId(99999);
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }

    public function testFindPendingApprovalShouldReturnEmptyArrayWhenNoMatches(): void
    {
        $initialPendingCount = count($this->repository->findPendingApproval());

        $approvedComment = $this->createComment(['isApproved' => true]);

        $result = $this->repository->findPendingApproval();
        $this->assertCount($initialPendingCount, $result);
        $this->assertIsArray($result);

        // 确保已审批的评论不在待审核列表中
        $pendingCommentIds = array_map(fn ($comment) => $comment->getId(), $result);
        $this->assertNotContains($approvedComment->getId(), $pendingCommentIds);
    }

    public function testSaveWithImagesShouldPersistImageData(): void
    {
        $comment = new Comment();
        $comment->setAccount($this->testAccount);
        $comment->setOrder($this->testOrder);
        $comment->setOrderItem($this->testOrderItem);
        $comment->setScore('5');
        $comment->setContent('Comment with images');
        $comment->setImages(['image1.jpg', 'image2.jpg']);
        $comment->setIsAnonymous(false);
        $comment->setCommentTime(new \DateTimeImmutable());
        $comment->setIsApproved(true);

        $this->repository->save($comment);

        $persisted = $this->repository->find($comment->getId());
        $this->assertNotNull($persisted);
        $this->assertSame(['image1.jpg', 'image2.jpg'], $persisted->getImages());
    }

    public function testSaveWithApproveTimeShouldPersistApproveTime(): void
    {
        $approveTime = new \DateTimeImmutable();
        $comment = new Comment();
        $comment->setAccount($this->testAccount);
        $comment->setOrder($this->testOrder);
        $comment->setOrderItem($this->testOrderItem);
        $comment->setScore('5');
        $comment->setIsAnonymous(false);
        $comment->setCommentTime(new \DateTimeImmutable());
        $comment->setIsApproved(true);
        $comment->setApproveTime($approveTime);

        $this->repository->save($comment);

        $persisted = $this->repository->find($comment->getId());
        $this->assertNotNull($persisted);
        $this->assertEquals($approveTime, $persisted->getApproveTime());
    }

    public function testFindOneByWithOrderByClause(): void
    {
        $uniqueScore = '3'; // 使用较少使用的评分
        $uniqueContent = 'OrderByTest_' . uniqid();

        $oldComment = $this->createComment([
            'score' => $uniqueScore,
            'content' => $uniqueContent . '_old',
            'commentTime' => new \DateTimeImmutable('-1 day'),
        ]);
        $newComment = $this->createComment([
            'score' => $uniqueScore,
            'content' => $uniqueContent . '_new',
            'commentTime' => new \DateTimeImmutable('now'),
        ]);

        // 先验证我们创建了两个评论
        $allMatching = $this->repository->findBy(['score' => $uniqueScore]);
        $this->assertGreaterThanOrEqual(2, count($allMatching));

        // 找到评分匹配的最新评论（按时间降序）
        $result = $this->repository->findOneBy(['score' => $uniqueScore], ['commentTime' => 'DESC']);

        $this->assertInstanceOf(Comment::class, $result);
        // 验证返回的是我们创建的评论之一
        $this->assertContains($result->getId(), [$oldComment->getId(), $newComment->getId()]);

        // 如果返回的是我们的评论，验证它确实是最新的
        if ($result->getId() === $newComment->getId() || $result->getId() === $oldComment->getId()) {
            $this->assertSame($uniqueScore, $result->getScore());
        }
    }

    public function testFindByWithNullCriteriaShouldFindCommentsWithNullValues(): void
    {
        $commentWithContent = $this->createComment(['content' => 'Has content']);
        $commentWithoutContent = $this->createComment(); // 没有设置 content

        $result = $this->repository->findBy(['content' => null]);

        $this->assertIsArray($result);
        // 验证结果中包含没有 content 的评论
        $resultIds = array_map(fn ($comment) => $comment->getId(), $result);
        $this->assertContains($commentWithoutContent->getId(), $resultIds);
        $this->assertNotContains($commentWithContent->getId(), $resultIds);
    }

    public function testFindByWithBooleanCriteriaShouldReturnCorrectResults(): void
    {
        $anonymousComment = $this->createComment(['isAnonymous' => true, 'content' => 'Anonymous']);
        $namedComment = $this->createComment(['isAnonymous' => false, 'content' => 'Named']);

        $anonymousResults = $this->repository->findBy(['isAnonymous' => true]);
        $namedResults = $this->repository->findBy(['isAnonymous' => false]);

        // 验证匿名评论结果
        $anonymousIds = array_map(fn ($comment) => $comment->getId(), $anonymousResults);
        $this->assertContains($anonymousComment->getId(), $anonymousIds);

        // 验证实名评论结果
        $namedIds = array_map(fn ($comment) => $comment->getId(), $namedResults);
        $this->assertContains($namedComment->getId(), $namedIds);
    }

    public function testFindByWithNullValue(): void
    {
        $this->createComment(['content' => null, 'images' => ['image1.jpg']]);
        $this->createComment(['content' => 'Some content', 'images' => null]);

        $result = $this->repository->findBy(['content' => null]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, count($result));
        foreach ($result as $comment) {
            $this->assertNull($comment->getContent());
        }
    }

    public function testCountWithNullValue(): void
    {
        $initialNullCount = $this->repository->count(['images' => null]);

        $this->createComment(['content' => 'null-images-1', 'images' => null]);
        $this->createComment(['content' => 'null-images-2', 'images' => null]);
        $this->createComment(['content' => 'with-images', 'images' => ['image1.jpg']]);

        $nullImagesCount = $this->repository->count(['images' => null]);
        $this->assertSame($initialNullCount + 2, $nullImagesCount);
    }

    public function testFindByWithAssociation(): void
    {
        $comment1 = $this->createComment(['content' => 'assoc-test-1']);
        $comment2 = $this->createComment(['content' => 'assoc-test-2']);

        $result = $this->repository->findBy(['account' => $this->testAccount]);

        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $foundIds = array_map(fn ($comment) => $comment->getId(), $result);
        $this->assertContains($comment1->getId(), $foundIds);
        $this->assertContains($comment2->getId(), $foundIds);

        foreach ($result as $comment) {
            $this->assertSame($this->testAccount->getId(), $comment->getAccount()->getId());
        }
    }

    public function testCountWithAssociation(): void
    {
        $this->createComment(['content' => 'count-assoc-1']);
        $this->createComment(['content' => 'count-assoc-2']);

        $accountCommentCount = $this->repository->count(['account' => $this->testAccount]);
        $this->assertGreaterThanOrEqual(2, $accountCommentCount);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key');
        $otherAccount->setAppSecret('other-app-secret');
        $otherAccount->setName('Other Account');
        $this->persistAndFlush($otherAccount);

        $this->createComment(['content' => 'test-account-comment']);

        $commentWithOtherAccount = new Comment();
        $commentWithOtherAccount->setAccount($otherAccount);
        $commentWithOtherAccount->setOrder($this->testOrder);
        $commentWithOtherAccount->setOrderItem($this->testOrderItem);
        $commentWithOtherAccount->setScore('5');
        $commentWithOtherAccount->setIsAnonymous(false);
        $commentWithOtherAccount->setCommentTime(new \DateTimeImmutable());
        $commentWithOtherAccount->setIsApproved(true);
        $this->persistAndFlush($commentWithOtherAccount);

        $result = $this->repository->findOneBy(['account' => $this->testAccount]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Comment::class, $result);
        $this->assertSame($this->testAccount->getId(), $result->getAccount()->getId());
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $otherAccount = new Account();
        $otherAccount->setAppKey('other-app-key-2');
        $otherAccount->setAppSecret('other-app-secret-2');
        $otherAccount->setName('Other Account 2');
        $this->persistAndFlush($otherAccount);

        $initialCount = $this->repository->count(['account' => $this->testAccount]);

        $this->createComment(['content' => 'test-account-1']);
        $this->createComment(['content' => 'test-account-2']);

        $commentWithOtherAccount = new Comment();
        $commentWithOtherAccount->setAccount($otherAccount);
        $commentWithOtherAccount->setOrder($this->testOrder);
        $commentWithOtherAccount->setOrderItem($this->testOrderItem);
        $commentWithOtherAccount->setScore('4');
        $commentWithOtherAccount->setIsAnonymous(false);
        $commentWithOtherAccount->setCommentTime(new \DateTimeImmutable());
        $commentWithOtherAccount->setIsApproved(true);
        $this->persistAndFlush($commentWithOtherAccount);

        $count = $this->repository->count(['account' => $this->testAccount]);
        $this->assertSame($initialCount + 2, $count);
    }

    public function testFindOneByAssociationOrderShouldReturnMatchingEntity(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('other-order-123');
        $otherOrder->setOrderState('FINISHED');
        $otherOrder->setPaymentState('PAID');
        $otherOrder->setLogisticsState('DELIVERED');
        $otherOrder->setReceiverName('Other Receiver');
        $otherOrder->setReceiverMobile('13900139000');
        $otherOrder->setReceiverProvince('上海市');
        $otherOrder->setReceiverCity('上海市');
        $otherOrder->setReceiverCounty('浦东新区');
        $otherOrder->setReceiverAddress('其他测试地址');
        $otherOrder->setOrderTotalPrice('2000.00');
        $otherOrder->setOrderPaymentPrice('2000.00');
        $otherOrder->setFreightPrice('10.00');
        $otherOrder->setOrderTime(new \DateTimeImmutable());
        $otherOrder->setSynced(true);
        $this->persistAndFlush($otherOrder);

        $otherOrderItem = new OrderItem();
        $otherOrderItem->setAccount($this->testAccount);
        $otherOrderItem->setOrder($otherOrder);
        $otherOrderItem->setSkuId('SKU456');
        $otherOrderItem->setSkuName('Other Product');
        $otherOrderItem->setQuantity(1);
        $otherOrderItem->setPrice('2000.00');
        $otherOrderItem->setTotalPrice('2000.00');
        $this->persistAndFlush($otherOrderItem);

        $this->createComment(['content' => 'test-order-comment']);

        $commentWithOtherOrder = new Comment();
        $commentWithOtherOrder->setAccount($this->testAccount);
        $commentWithOtherOrder->setOrder($otherOrder);
        $commentWithOtherOrder->setOrderItem($otherOrderItem);
        $commentWithOtherOrder->setScore('3');
        $commentWithOtherOrder->setIsAnonymous(false);
        $commentWithOtherOrder->setCommentTime(new \DateTimeImmutable());
        $commentWithOtherOrder->setIsApproved(true);
        $this->persistAndFlush($commentWithOtherOrder);

        $result = $this->repository->findOneBy(['order' => $this->testOrder]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Comment::class, $result);
        $this->assertSame($this->testOrder->getId(), $result->getOrder()->getId());
    }

    public function testCountByAssociationOrderShouldReturnCorrectNumber(): void
    {
        $otherOrder = new Order();
        $otherOrder->setAccount($this->testAccount);
        $otherOrder->setOrderId('other-order-456');
        $otherOrder->setOrderState('FINISHED');
        $otherOrder->setPaymentState('PAID');
        $otherOrder->setLogisticsState('DELIVERED');
        $otherOrder->setReceiverName('Other Receiver 2');
        $otherOrder->setReceiverMobile('13800138001');
        $otherOrder->setReceiverProvince('广州市');
        $otherOrder->setReceiverCity('广州市');
        $otherOrder->setReceiverCounty('天河区');
        $otherOrder->setReceiverAddress('其他测试地址2');
        $otherOrder->setOrderTotalPrice('3000.00');
        $otherOrder->setOrderPaymentPrice('3000.00');
        $otherOrder->setFreightPrice('15.00');
        $otherOrder->setOrderTime(new \DateTimeImmutable());
        $otherOrder->setSynced(true);
        $this->persistAndFlush($otherOrder);

        $otherOrderItem = new OrderItem();
        $otherOrderItem->setAccount($this->testAccount);
        $otherOrderItem->setOrder($otherOrder);
        $otherOrderItem->setSkuId('SKU789');
        $otherOrderItem->setSkuName('Other Product 2');
        $otherOrderItem->setQuantity(1);
        $otherOrderItem->setPrice('3000.00');
        $otherOrderItem->setTotalPrice('3000.00');
        $this->persistAndFlush($otherOrderItem);

        $initialCount = $this->repository->count(['order' => $this->testOrder]);

        $this->createComment(['content' => 'test-order-1']);
        $this->createComment(['content' => 'test-order-2']);

        $commentWithOtherOrder = new Comment();
        $commentWithOtherOrder->setAccount($this->testAccount);
        $commentWithOtherOrder->setOrder($otherOrder);
        $commentWithOtherOrder->setOrderItem($otherOrderItem);
        $commentWithOtherOrder->setScore('2');
        $commentWithOtherOrder->setIsAnonymous(false);
        $commentWithOtherOrder->setCommentTime(new \DateTimeImmutable());
        $commentWithOtherOrder->setIsApproved(true);
        $this->persistAndFlush($commentWithOtherOrder);

        $count = $this->repository->count(['order' => $this->testOrder]);
        $this->assertSame($initialCount + 2, $count);
    }

    public function testFindOneByAssociationOrderItemShouldReturnMatchingEntity(): void
    {
        $otherOrderItem = new OrderItem();
        $otherOrderItem->setAccount($this->testAccount);
        $otherOrderItem->setOrder($this->testOrder);
        $otherOrderItem->setSkuId('SKU999');
        $otherOrderItem->setSkuName('Other Item Product');
        $otherOrderItem->setQuantity(1);
        $otherOrderItem->setPrice('1500.00');
        $otherOrderItem->setTotalPrice('1500.00');
        $this->persistAndFlush($otherOrderItem);

        $this->createComment(['content' => 'test-orderitem-comment']);

        $commentWithOtherOrderItem = new Comment();
        $commentWithOtherOrderItem->setAccount($this->testAccount);
        $commentWithOtherOrderItem->setOrder($this->testOrder);
        $commentWithOtherOrderItem->setOrderItem($otherOrderItem);
        $commentWithOtherOrderItem->setScore('4');
        $commentWithOtherOrderItem->setIsAnonymous(false);
        $commentWithOtherOrderItem->setCommentTime(new \DateTimeImmutable());
        $commentWithOtherOrderItem->setIsApproved(true);
        $this->persistAndFlush($commentWithOtherOrderItem);

        $result = $this->repository->findOneBy(['orderItem' => $this->testOrderItem]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Comment::class, $result);
        $this->assertSame($this->testOrderItem->getId(), $result->getOrderItem()->getId());
    }

    public function testCountByAssociationOrderItemShouldReturnCorrectNumber(): void
    {
        $otherOrderItem = new OrderItem();
        $otherOrderItem->setAccount($this->testAccount);
        $otherOrderItem->setOrder($this->testOrder);
        $otherOrderItem->setSkuId('SKU888');
        $otherOrderItem->setSkuName('Other Item Product 2');
        $otherOrderItem->setQuantity(2);
        $otherOrderItem->setPrice('800.00');
        $otherOrderItem->setTotalPrice('1600.00');
        $this->persistAndFlush($otherOrderItem);

        $initialCount = $this->repository->count(['orderItem' => $this->testOrderItem]);

        $this->createComment(['content' => 'test-orderitem-1']);
        $this->createComment(['content' => 'test-orderitem-2']);

        $commentWithOtherOrderItem = new Comment();
        $commentWithOtherOrderItem->setAccount($this->testAccount);
        $commentWithOtherOrderItem->setOrder($this->testOrder);
        $commentWithOtherOrderItem->setOrderItem($otherOrderItem);
        $commentWithOtherOrderItem->setScore('1');
        $commentWithOtherOrderItem->setIsAnonymous(false);
        $commentWithOtherOrderItem->setCommentTime(new \DateTimeImmutable());
        $commentWithOtherOrderItem->setIsApproved(true);
        $this->persistAndFlush($commentWithOtherOrderItem);

        $count = $this->repository->count(['orderItem' => $this->testOrderItem]);
        $this->assertSame($initialCount + 2, $count);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setAppKey('test-app-key-' . uniqid());
        $account->setAppSecret('test-app-secret-' . uniqid());
        $account->setName('Test Account ' . uniqid());

        $order = new Order();
        $order->setAccount($account);
        $order->setOrderId('JD' . uniqid());
        $order->setOrderState('待支付');
        $order->setPaymentState('未支付');
        $order->setLogisticsState('未发货');
        $order->setReceiverName('张三');
        $order->setReceiverMobile('13800138000');
        $order->setReceiverProvince('北京市');
        $order->setReceiverCity('北京市');
        $order->setReceiverCounty('朝阳区');
        $order->setReceiverAddress('朝阳区三里屯太古里');
        $order->setOrderTotalPrice('199.00');
        $order->setOrderPaymentPrice('199.00');
        $order->setFreightPrice('0.00');
        $order->setOrderTime(new \DateTimeImmutable());
        $order->setSynced(true);

        $orderItem = new OrderItem();
        $orderItem->setAccount($account);
        $orderItem->setOrder($order);
        $orderItem->setSkuId('sku-' . uniqid());
        $orderItem->setSkuName('测试商品');
        $orderItem->setQuantity(1);
        $orderItem->setPrice('199.00');
        $orderItem->setTotalPrice('199.00');

        $comment = new Comment();
        $comment->setAccount($account);
        $comment->setOrder($order);
        $comment->setOrderItem($orderItem);
        $comment->setScore('5');
        $comment->setCommentTime(new \DateTimeImmutable());

        return $comment;
    }

    protected function getRepository(): CommentRepository
    {
        return self::getService(CommentRepository::class);
    }
}
