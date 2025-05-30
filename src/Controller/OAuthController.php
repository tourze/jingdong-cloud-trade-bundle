<?php

namespace JingdongCloudTradeBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OAuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/oauth/callback', name: 'jingdong_pop_oauth_callback')]
    public function callback(Request $request): Response
    {
        $accountId = $request->getSession()->get('jingdong_pop_account_id');
        if (!$accountId) {
            throw new \RuntimeException('No account ID in session');
        }

        $account = $this->entityManager->find(Account::class, $accountId);
        if (!$account) {
            throw new \RuntimeException('Account not found');
        }

        $this->authService->handleCallback($account, $request);
        $this->entityManager->flush();

        return new Response('授权成功');
    }

    #[Route('/oauth/authorize/{id}', name: 'jingdong_pop_oauth_authorize')]
    public function authorize(Request $request, Account $account): Response
    {
        $redirectUri = $this->generateUrl('jingdong_pop_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $url = $this->authService->getAuthorizationUrl($account, $redirectUri);

        $this->entityManager->flush();

        $request->getSession()->set('jingdong_pop_account_id', $account->getId());

        return $this->redirect($url);
    }
}
