<?php

namespace JingdongCloudTradeBundle\Service;

use JingdongCloudTradeBundle\Entity\Account;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthService
{
    private const AUTHORIZE_URL = 'https://oauth.jd.com/oauth/authorize';
    private const TOKEN_URL = 'https://oauth.jd.com/oauth/token';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getAuthorizationUrl(Account $account, string $redirectUri, array $scope = []): string
    {
        $state = bin2hex(random_bytes(16));
        $account->setState($state);

        $params = [
            'response_type' => 'code',
            'client_id' => $account->getAppKey(),
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ];

        if (!empty($scope)) {
            $params['scope'] = implode(' ', $scope);
        }

        return self::AUTHORIZE_URL . '?' . http_build_query($params);
    }

    public function handleCallback(Account $account, Request $request): void
    {
        $state = $request->query->get('state');
        if ($state !== $account->getState()) {
            throw new \RuntimeException('Invalid state');
        }

        $code = $request->query->get('code');
        if (!$code) {
            throw new \RuntimeException('No code received');
        }

        $account->setCode($code);
        $account->setCodeExpiresAt(new \DateTimeImmutable('+10 minutes'));

        $this->getAccessToken($account);
    }

    public function getAccessToken(Account $account): void
    {
        if (!$account->isAccessTokenExpired()) {
            return;
        }

        if ($account->getRefreshToken() && !$account->isRefreshTokenExpired()) {
            $this->refreshAccessToken($account);
            return;
        }

        if (!$account->getCode() || $account->getCodeExpiresAt() < new \DateTimeImmutable()) {
            throw new \RuntimeException('No valid code available');
        }

        $response = $this->httpClient->request('POST', self::TOKEN_URL, [
            'body' => [
                'grant_type' => 'authorization_code',
                'client_id' => $account->getAppKey(),
                'client_secret' => $account->getAppSecret(),
                'code' => $account->getCode(),
                'redirect_uri' => $this->urlGenerator->generate('jingdong_pop_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ]);

        $data = $response->toArray();

        $this->updateTokens($account, $data);
    }

    private function refreshAccessToken(Account $account): void
    {
        $response = $this->httpClient->request('POST', self::TOKEN_URL, [
            'body' => [
                'grant_type' => 'refresh_token',
                'client_id' => $account->getAppKey(),
                'client_secret' => $account->getAppSecret(),
                'refresh_token' => $account->getRefreshToken(),
            ],
        ]);

        $data = $response->toArray();

        $this->updateTokens($account, $data);
    }

    private function updateTokens(Account $account, array $data): void
    {
        if (!isset($data['access_token'])) {
            throw new \RuntimeException('No access token received');
        }

        $account->setAccessToken($data['access_token']);
        $account->setAccessTokenExpiresAt(new \DateTimeImmutable(sprintf('+%d seconds', $data['expires_in'] ?? 3600)));

        if (isset($data['refresh_token'])) {
            $account->setRefreshToken($data['refresh_token']);
            $account->setRefreshTokenExpiresAt(new \DateTimeImmutable(sprintf('+%d seconds', $data['refresh_token_expires_in'] ?? 2592000)));
        }
    }
}
