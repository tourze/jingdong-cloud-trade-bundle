<?php

namespace JingdongCloudTradeBundle\Service;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Exception\OAuthException;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'jingdong_cloud_trade')]
class AuthService
{
    private const AUTHORIZE_URL = 'https://oauth.jd.com/oauth/authorize';
    private const TOKEN_URL = 'https://oauth.jd.com/oauth/token';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string> $scope
     */
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

        if ([] !== $scope) {
            $params['scope'] = implode(' ', $scope);
        }

        return self::AUTHORIZE_URL . '?' . http_build_query($params);
    }

    public function handleCallback(Account $account, Request $request): void
    {
        $state = $request->query->get('state');
        if ($state !== $account->getState()) {
            throw new OAuthException('Invalid state');
        }

        $code = $request->query->get('code');
        if (null === $code || '' === $code || false === $code) {
            throw new OAuthException('No code received');
        }

        $account->setCode((string) $code);
        $account->setCodeExpiresAt(new \DateTimeImmutable('+10 minutes'));

        $this->getAccessToken($account);
    }

    public function getAccessToken(Account $account): void
    {
        if (!$account->isAccessTokenExpired()) {
            return;
        }

        if (null !== $account->getRefreshToken() && !$account->isRefreshTokenExpired()) {
            $this->refreshAccessToken($account);

            return;
        }

        if (null === $account->getCode() || $account->getCodeExpireTime() < new \DateTimeImmutable()) {
            throw new OAuthException('No valid code available');
        }

        $startTime = microtime(true);
        try {
            $response = $this->httpClient->request('POST', self::TOKEN_URL, [
                'body' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $account->getAppKey(),
                    'client_secret' => $account->getAppSecret(),
                    'code' => $account->getCode(),
                    'redirect_uri' => $this->urlGenerator->generate('jingdong_pop_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
            ]);
            $this->logger->info('京东OAuth获取访问令牌成功', [
                'account_id' => $account->getId(),
                'duration' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('京东OAuth获取访问令牌失败', [
                'account_id' => $account->getId(),
                'error' => $e->getMessage(),
                'duration' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            ]);
            throw $e;
        }

        /** @var array<string, mixed> $data */
        $data = $response->toArray();

        $this->updateTokens($account, $data);
    }

    private function refreshAccessToken(Account $account): void
    {
        $startTime = microtime(true);
        try {
            $response = $this->httpClient->request('POST', self::TOKEN_URL, [
                'body' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => $account->getAppKey(),
                    'client_secret' => $account->getAppSecret(),
                    'refresh_token' => $account->getRefreshToken(),
                ],
            ]);
            $this->logger->info('京东OAuth刷新访问令牌成功', [
                'account_id' => $account->getId(),
                'duration' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('京东OAuth刷新访问令牌失败', [
                'account_id' => $account->getId(),
                'error' => $e->getMessage(),
                'duration' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            ]);
            throw $e;
        }

        /** @var array<string, mixed> $data */
        $data = $response->toArray();

        $this->updateTokens($account, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function updateTokens(Account $account, array $data): void
    {
        if (!isset($data['access_token']) || !is_string($data['access_token'])) {
            throw new OAuthException('No valid access token received');
        }

        $account->setAccessToken($data['access_token']);

        $expiresIn = $data['expires_in'] ?? 3600;
        if (!is_int($expiresIn) && !is_string($expiresIn) && !is_float($expiresIn)) {
            $expiresIn = 3600;
        }
        $account->setAccessTokenExpiresAt(new \DateTimeImmutable(sprintf('+%d seconds', (int) $expiresIn)));

        if (isset($data['refresh_token']) && is_string($data['refresh_token'])) {
            $account->setRefreshToken($data['refresh_token']);

            $refreshExpiresIn = $data['refresh_token_expires_in'] ?? 2592000;
            if (!is_int($refreshExpiresIn) && !is_string($refreshExpiresIn) && !is_float($refreshExpiresIn)) {
                $refreshExpiresIn = 2592000;
            }
            $account->setRefreshTokenExpiresAt(new \DateTimeImmutable(sprintf('+%d seconds', (int) $refreshExpiresIn)));
        }
    }
}
