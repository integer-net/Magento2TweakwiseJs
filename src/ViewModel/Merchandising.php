<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\ViewModel;

use Magento\Catalog\Block\Category\View;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Tweakwise\TweakwiseJs\Helper\Data;

class Merchandising implements ArgumentInterface
{
    /**
     * @param Data $dataHelper
     * @param StoreManagerInterface $storeManager
     * @param FormKey $formKey
     */
    public function __construct(
        private readonly Data $dataHelper,
        private readonly StoreManagerInterface $storeManager,
        private readonly FormKey $formKey
    ) {
    }

    /**
     * @param View $block
     * @return string
     */
    public function getTweakwiseCategoryId(View $block): string
    {
        try {
            return $this->dataHelper->getTweakwiseId((int)$block->getCurrentCategory()->getId());
        } catch (NoSuchEntityException $e) {
            return '0';
        }
    }

    /**
     * @return string
     */
    public function getStoreId(): string
    {
        try {
            return (string)$this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            return '0';
        }
    }

    /**
     * @return string
     */
    public function getFormKey(): string
    {
        try {
            return $this->formKey->getFormKey();
        } catch (LocalizedException $e) {
            return '';
        }
    }
}
