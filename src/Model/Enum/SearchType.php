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
            self::MAGENTO_DEFAULT => 'Magento Default',
            self::INSTANT_SEARCH => 'Instant Search',
            self::SUGGESTIONS => 'Suggestions'
        };
    }
}
