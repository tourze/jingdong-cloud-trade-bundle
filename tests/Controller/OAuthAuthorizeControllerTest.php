<?php

namespace JingdongCloudTradeBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Controller\OAuthAuthorizeController;
use JingdongCloudTradeBundle\Entity\Account;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(OAuthAuthorizeController::class)]
#[RunTestsInSeparateProcesses]
final class OAuthAuthorizeControllerTest extends AbstractWebTestCase
{
    public function testGetAuthorizeRedirect(): void
    {
        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');
        $account->setName('Test Account');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();

        $client->request('GET', '/oauth/authorize/' . $account->getId());

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(302, $response->getStatusCode());

        $location = $response->headers->get('Location');
        $this->assertIsString($location);
        $this->assertStringContainsString('oauth.jd.com', $location);
        $this->assertStringContainsString('test_key', $location);

        $this->assertEquals($account->getId(), $client->getRequest()->getSession()->get('jingdong_pop_account_id'));
    }

    public function testPostAuthorizeNotAllowed(): void
    {
        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');
        $account->setName('Test Account');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('POST', '/oauth/authorize/' . $account->getId());
    }

    public function testPutAuthorizeNotAllowed(): void
    {
        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');
        $account->setName('Test Account');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PUT', '/oauth/authorize/' . $account->getId());
    }

    public function testDeleteAuthorizeNotAllowed(): void
    {
        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');
        $account->setName('Test Account');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/oauth/authorize/' . $account->getId());
    }

    public function testPatchAuthorizeNotAllowed(): void
    {
        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');
        $account->setName('Test Account');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PATCH', '/oauth/authorize/' . $account->getId());
    }

    public function testHeadAuthorizeAllowed(): void
    {
        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');
        $account->setName('Test Account');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();

        $client->request('HEAD', '/oauth/authorize/' . $account->getId());

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testOptionsAuthorizeNotAllowed(): void
    {
        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');
        $account->setName('Test Account');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('OPTIONS', '/oauth/authorize/' . $account->getId());
    }

    public function testUnauthorizedAccessWithInvalidAccountId(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(NotFoundHttpException::class);
        $client->request('GET', '/oauth/authorize/999999');
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        if ('INVALID' === $method) {
            self::markTestSkipped('No disallowed methods found');
        }

        $client = self::createClientWithDatabase();

        $account = new Account();
        $account->setAppKey('test_key');
        $account->setAppSecret('test_secret');
        $account->setName('Test Account');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($account);
        $entityManager->flush();

        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);

        // PHPStan 规则要求 HTTP method 必须是字符串字面量
        match ($method) {
            'POST' => $client->request('POST', '/oauth/authorize/' . $account->getId()),
            'PUT' => $client->request('PUT', '/oauth/authorize/' . $account->getId()),
            'DELETE' => $client->request('DELETE', '/oauth/authorize/' . $account->getId()),
            'PATCH' => $client->request('PATCH', '/oauth/authorize/' . $account->getId()),
            'OPTIONS' => $client->request('OPTIONS', '/oauth/authorize/' . $account->getId()),
            'TRACE' => $client->request('TRACE', '/oauth/authorize/' . $account->getId()),
            'PURGE' => $client->request('PURGE', '/oauth/authorize/' . $account->getId()),
            default => self::markTestSkipped("Unsupported method: {$method}"),
        };
    }
}
