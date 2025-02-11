<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Model\Config\Source;

use Magento\Config\Model\Config\Source\Yesno as MagentoYesno;
use Tweakwise\TweakwiseJs\Model\Api\Client;

class Yesno extends MagentoYesno
{
    /**
     * @param Client $apiClient
     */
    public function __construct(
        private readonly Client $apiClient
    ) {
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->apiClient->isNavigationFeatureEnabled()) {
            return parent::toOptionArray();
        }

        return [['value' => 0, 'label' => __('No')]];
    }
}
