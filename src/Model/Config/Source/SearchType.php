<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Tweakwise\TweakwiseJs\Model\Enum\SearchType as SearchTypeEnum;

class SearchType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return array_map(function (SearchTypeEnum $searchTypeEnum) {
            return ['value' => $searchTypeEnum->value, 'label' => $searchTypeEnum->label()];
        }, SearchTypeEnum::cases());
    }
}
