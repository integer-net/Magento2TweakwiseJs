<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Model\Api;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Tweakwise\TweakwiseJs\Api\Exception\ApiException;
use Tweakwise\TweakwiseJs\Helper\Data;
use Tweakwise\TweakwiseJs\Model\Config;

class Client
{
    /**
     * @param Config $config
     * @param Json $jsonSerializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly Config $config,
        private readonly Json $jsonSerializer,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @return bool
     */
    public function isNavigationFeatureEnabled(): bool
    {
        $url = sprintf(
            '%s/instance/%s',
            Data::GATEWAY_TWEAKWISE_NAVIGATOR_COM_URL,
            $this->config->getInstanceKey()
        );

        try {
            $response = $this->doRequest($url);
        } catch (ApiException $e) {
            $this->logger->critical(
                'Tweakwise API error: Unable to retrieve navigation feature enabled',
                [
                    'url' => $url,
                    'exception' => $e->getMessage()
                ]
            );
            return true;
        }

        $features = $response['features'] ?? [];
        foreach ($features as $feature) {
            if ($feature['name'] !== 'navigation') {
                continue;
            }

            return $feature['value'];
        }

        return true;
    }

    /**
     * @param string $url
     * @param string $method
     * @return array
     * @throws ApiException
     */
    private function doRequest(string $url, string $method = 'GET'): array
    {
        $httpClient = new HttpClient(
            [
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]
        );

        try {
            $response = $httpClient->request($method, $url);
        } catch (GuzzleException $e) {
            throw new ApiException('An error occurred while retrieving data via the API', previous: $e);
        }

        $contents = $response->getBody()->getContents();
        return $this->jsonSerializer->unserialize($contents);
    }
}
