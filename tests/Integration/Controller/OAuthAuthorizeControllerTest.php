<?php

namespace JingdongCloudTradeBundle\Tests\Integration\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Controller\OAuthAuthorizeController;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Service\AuthService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OAuthAuthorizeControllerTest extends TestCase
{
    private AuthService $authService;
    private EntityManagerInterface $entityManager;
    private OAuthAuthorizeController $controller;

    protected function setUp(): void
    {
        $this->authService = $this->createMock(AuthService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->controller = new OAuthAuthorizeController(
            $this->authService,
            $this->entityManager
        );
        
        // 模拟 generateUrl 方法
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->with('jingdong_pop_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/oauth/callback');
            
        $this->controller->setContainer($this->createMockContainer($urlGenerator));
    }

    public function testInvoke(): void
    {
        $account = new Account();
        $account->setAppKey('test_app_key');
        
        // 使用反射设置 ID
        $reflection = new \ReflectionClass($account);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($account, 123);

        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $authUrl = 'https://oauth.jd.com/oauth/authorize?response_type=code&client_id=test_app_key&redirect_uri=https%3A%2F%2Fexample.com%2Foauth%2Fcallback&state=test_state';

        $this->authService->expects($this->once())
            ->method('getAuthorizationUrl')
            ->with($account, 'https://example.com/oauth/callback')
            ->willReturn($authUrl);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $response = $this->controller->__invoke($request, $account);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($authUrl, $response->getTargetUrl());
        $this->assertEquals(123, $session->get('jingdong_pop_account_id'));
    }

    private function createMockContainer(UrlGeneratorInterface $urlGenerator)
    {
        $container = $this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class);
        $container->method('get')
            ->with('router')
            ->willReturn($urlGenerator);
        $container->method('has')
            ->with('router')
            ->willReturn(true);
        
        return $container;
    }
}