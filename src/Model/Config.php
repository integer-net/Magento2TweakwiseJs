<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'tweakwisejs/general/enabled';
    private const XML_PATH_INSTANCE_KEY = 'tweakwisejs/general/instance_key';

    public const XML_PATH_MERCHANDISING_ENABLED = 'tweakwisejs/merchandising/enabled';

    private const XML_PATH_SEARCH_TYPE = 'tweakwisejs/search/type';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    public function getInstanceKey(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INSTANCE_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param string $scopeType
     * @param null|int|string $scopeCode
     * @return bool
     */
    public function isMerchandisingEnabled(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        mixed $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_MERCHANDISING_ENABLED, $scopeType, $scopeCode);
    }

    /**
     * @return string
     */
    public function getSearchType(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEARCH_TYPE, ScopeInterface::SCOPE_STORE);
    }
}
