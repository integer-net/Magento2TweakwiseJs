<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Plugin\Helper;

use Magento\Search\Helper\Data as Subject;
use Tweakwise\TweakwiseJs\Model\Config;
use Tweakwise\TweakwiseJs\Model\Enum\SearchType;

class Data
{
    /**
     * @param Config $config
     */
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * If search type is not the default Magento search -> disable the Magento suggestions by setting empty URL
     * @param Subject $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSuggestUrl(Subject $subject, string $result): string
    {
        if (!$this->config->isEnabled() || $this->config->getSearchType() === SearchType::MAGENTO_DEFAULT->value) {
            return $result;
        }

        return '';
    }
}
