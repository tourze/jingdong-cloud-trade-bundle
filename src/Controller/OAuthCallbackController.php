<?php

namespace JingdongCloudTradeBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Exception\AccountNotFoundException;
use JingdongCloudTradeBundle\Exception\OAuthException;
use JingdongCloudTradeBundle\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OAuthCallbackController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(path: '/oauth/callback', name: 'jingdong_pop_oauth_callback', methods: ['GET', 'HEAD'])]
    public function __invoke(Request $request): Response
    {
        $accountId = $request->getSession()->get('jingdong_pop_account_id');
        if (null === $accountId) {
            throw new OAuthException('No account ID in session');
        }

        $account = $this->entityManager->find(Account::class, $accountId);
        if (null === $account) {
            throw new AccountNotFoundException('Account not found');
        }

        $this->authService->handleCallback($account, $request);
        $this->entityManager->flush();

        return new Response('授权成功');
    }
}
