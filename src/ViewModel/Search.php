<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Tweakwise\TweakwiseJs\Helper\Data;
use Tweakwise\TweakwiseJs\Model\Config;

class Search implements ArgumentInterface
{
    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param Data $dataHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager,
        private readonly Data $dataHelper,
        private readonly UrlInterface $urlBuilder
    ) {
    }

    /**
     * @return string
     */
    public function getSearchType(): string
    {
        return $this->config->getSearchType();
    }

    /**
     * @return string|null
     */
    public function getInstanceKey(): ?string
    {
        return $this->config->getInstanceKey();
    }

    /**
     * @return int
     */
    public function getStoreRootCategory(): int
    {
        try {
            return (int)$this->dataHelper->getTweakwiseId(
                (int)$this->storeManager->getStore()->getRootCategoryId()
            );
        } catch (NoSuchEntityException $e) {
            return 0;
        }
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return trim($this->urlBuilder->getUrl('twsearch#twn|'), '/');
    }
}
