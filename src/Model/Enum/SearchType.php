<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Model\Enum;

enum SearchType: string
{
    case MAGENTO_DEFAULT = 'magento_default';
    case INSTANT_SEARCH = 'instant_search';
    case SUGGESTIONS = 'suggestions';

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::MAGENTO_DEFAULT => __('Magento Default')->render(),
            self::INSTANT_SEARCH => __('Instant Search')->render(),
            self::SUGGESTIONS => __('Suggestions')->render()
        };
    }
}
