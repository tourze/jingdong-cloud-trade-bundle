<?php

namespace JingdongCloudTradeBundle\Service;

use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Exception\ApiException;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[WithMonologChannel(channel: 'jingdong_cloud_trade')]
class Client
{
    private string $serverUrl = 'https://api.jd.com/routerjson';

    private string $version = '2.0';

    private string $format = 'json';

    private string $jsonParamKey = '360buy_param_json';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly AuthService $authService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string, mixed> $params
     */
    private function generateSign(array $params, string $appSecret): string
    {
        ksort($params);
        $stringToBeSigned = $appSecret;
        foreach ($params as $k => $v) {
            $stringValue = is_string($v) || is_numeric($v) || is_bool($v) ? (string) $v : '';
            if (!str_starts_with($stringValue, '@')) {
                $stringToBeSigned .= "{$k}{$stringValue}";
            }
        }
        unset($k, $v, $stringValue);
        $stringToBeSigned .= $appSecret;

        return strtoupper(md5($stringToBeSigned));
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
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

        $startTime = microtime(true);
        try {
            $response = $this->httpClient->request('POST', $this->serverUrl, [
                'body' => $systemParams,
            ]);

            $result = $response->toArray();
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            if (isset($result['error_response'])) {
                $errorResponse = $result['error_response'];
                $errorMsg = '未知错误';
                if (is_array($errorResponse) && isset($errorResponse['zh_desc'])) {
                    $zhDesc = $errorResponse['zh_desc'];
                    $errorMsg = is_string($zhDesc) || is_numeric($zhDesc) ? (string) $zhDesc : '未知错误';
                }

                $this->logger->error('京东API调用失败', [
                    'account_id' => $account->getId(),
                    'method' => $method,
                    'error' => $errorMsg,
                    'duration' => $duration . 'ms',
                ]);
                throw new ApiException($errorMsg);
            }

            $this->logger->info('京东API调用成功', [
                'account_id' => $account->getId(),
                'method' => $method,
                'duration' => $duration . 'ms',
            ]);

            /** @var array<string, mixed> $result */
            return $result;
        } catch (ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('京东API调用异常', [
                'account_id' => $account->getId(),
                'method' => $method,
                'error' => $e->getMessage(),
                'duration' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            ]);
            throw $e;
        }
    }
}
