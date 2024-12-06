<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'tweakwisejs/general/enabled';
    private const XML_PATH_INSTANCE_KEY = 'tweakwisejs/general/instance_key';

    private const XML_PATH_MERCHANDISING_ENABLED = 'tweakwisejs/merchandising/enabled';

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
     * @return bool
     */
    public function isMerchandisingEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_MERCHANDISING_ENABLED, ScopeInterface::SCOPE_STORE);
    }
}
