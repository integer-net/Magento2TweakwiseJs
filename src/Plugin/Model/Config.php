<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Plugin\Model;

use Magento\Config\Model\Config as Subject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Tweakwise\TweakwiseJs\Model\Api\Client;
use Tweakwise\TweakwiseJs\Model\Config as TweakwiseJsConfig;

class Config
{
    /**
     * @param Client $apiClient
     * @param TweakwiseJsConfig $tweakwiseJsConfig
     */
    public function __construct(
        private readonly Client $apiClient,
        private readonly TweakwiseJsConfig $tweakwiseJsConfig
    ) {
    }

    /**
     * @param Subject $subject
     * @return void
     * @throws LocalizedException
     */
    public function beforeSave(Subject $subject)
    {
        $data = $subject->getData();
        if (
            !$this->isTweakwiseConfigurationSaved($subject) ||
            !$this->isMerchandisingChangedToEnabled($data, $subject) ||
            $this->apiClient->isNavigationFeatureEnabled()
        ) {
            return;
        }

        throw new LocalizedException(
            __('Merchandising cannot be enabled because it is not enabled in your Tweakwise instance')
        );
    }

    /**
     * @param Subject $subject
     * @return bool
     */
    private function isTweakwiseConfigurationSaved(Subject $subject): bool
    {
        return $subject->getSection() === 'tweakwise';
    }

    /**
     * @param array $data
     * @param Subject $subject
     * @return bool
     */
    private function isMerchandisingChangedToEnabled(array $data, Subject $subject): bool
    {
        $newValue = (bool) $data['groups']['tweakwisejs']['groups']['merchandising']['fields']['enabled']['value'];

        if (!$newValue) {
            return false;
        }

        list($scopeType, $scopeCode) = $this->getScope(
            $subject->getData('website'),
            $subject->getData('store')
        );
        $oldValue = $this->tweakwiseJsConfig->isMerchandisingEnabled($scopeType, $scopeCode);
        return !$oldValue;
    }

    /**
     * @param string|null $websiteId
     * @param string|null $storeId
     * @return array
     */
    private function getScope(?string $websiteId, ?string $storeId): array
    {
        if ($websiteId) {
            return [ScopeInterface::SCOPE_WEBSITE, $websiteId];
        }

        if ($storeId) {
            return [ScopeInterface::SCOPE_STORE, $storeId];
        }

        return [ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null];
    }
}
