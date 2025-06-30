<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Controller\OAuthCallbackController;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Exception\AccountNotFoundException;
use JingdongCloudTradeBundle\Exception\OAuthException;
use JingdongCloudTradeBundle\Service\AuthService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class OAuthCallbackControllerTest extends TestCase
{
    private AuthService $authService;
    private EntityManagerInterface $entityManager;
    private OAuthCallbackController $controller;

    protected function setUp(): void
    {
        $this->authService = $this->createMock(AuthService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->controller = new OAuthCallbackController(
            $this->authService,
            $this->entityManager
        );
    }

    public function testInvoke_success(): void
    {
        $account = new Account();
        
        // 使用反射设置 ID
        $reflection = new \ReflectionClass($account);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($account, 123);

        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $session->set('jingdong_pop_account_id', 123);
        $request->setSession($session);

        $this->entityManager->expects($this->once())
            ->method('find')
            ->with(Account::class, 123)
            ->willReturn($account);

        $this->authService->expects($this->once())
            ->method('handleCallback')
            ->with($account, $request);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $response = $this->controller->__invoke($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('授权成功', $response->getContent());
    }

    public function testInvoke_noAccountIdInSession(): void
    {
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('No account ID in session');

        $this->controller->__invoke($request);
    }

    public function testInvoke_accountNotFound(): void
    {
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $session->set('jingdong_pop_account_id', 123);
        $request->setSession($session);

        $this->entityManager->expects($this->once())
            ->method('find')
            ->with(Account::class, 123)
            ->willReturn(null);

        $this->expectException(AccountNotFoundException::class);
        $this->expectExceptionMessage('Account not found');

        $this->controller->__invoke($request);
    }
}