<?php

namespace JingdongCloudTradeBundle\Service;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Exception\ApiException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private string $serverUrl = 'https://api.jd.com/routerjson';
    private string $version = '2.0';
    private string $format = 'json';
    private string $jsonParamKey = '360buy_param_json';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly AuthService $authService,
    ) {
    }

    private function generateSign(array $params, string $appSecret): string
    {
        ksort($params);
        $stringToBeSigned = $appSecret;
        foreach ($params as $k => $v) {
            if (!str_starts_with($v, '@')) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $appSecret;

        return strtoupper(md5($stringToBeSigned));
    }

    public function execute(Account $account, string $method, array $params = []): array
    {
        $this->authService->getAccessToken($account);

        $systemParams = [
            'v' => $this->version,
            'method' => $method,
            'app_key' => $account->getAppKey(),
            'access_token' => $account->getAccessToken(),
            'timestamp' => date('Y-m-d H:i:s'),
            'format' => $this->format,
            'sign_method' => 'md5',
        ];

        $systemParams[$this->jsonParamKey] = json_encode($params);
        $systemParams['sign'] = $this->generateSign($systemParams, $account->getAppSecret());

        $response = $this->httpClient->request('POST', $this->serverUrl, [
            'body' => $systemParams,
        ]);

        $result = $response->toArray();

        if (isset($result['error_response'])) {
            throw new ApiException($result['error_response']['zh_desc'] ?? '未知错误');
        }

        return $result;
    }
}
