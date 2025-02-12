<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Tweakwise\TweakwiseJs\Model\Api\Client;
use Tweakwise\TweakwiseJs\Model\Enum\SearchType as SearchTypeEnum;

class SearchType implements OptionSourceInterface
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
        return array_map(function (SearchTypeEnum $searchTypeEnum) {
            // If suggestions feature is not enabled, don't add this option
            if (
                $searchTypeEnum->value === SearchTypeEnum::SUGGESTIONS->value &&
                !$this->apiClient->isSuggestionsFeatureEnabled()
            ) {
                return ['value' => null, 'label' => null];
            }

            return ['value' => $searchTypeEnum->value, 'label' => $searchTypeEnum->label()];
        }, SearchTypeEnum::cases());
    }
}
