<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\ViewModel;

use Magento\Catalog\Block\Category\View;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tweakwise\TweakwiseJs\Helper\Data;

class Merchandising implements ArgumentInterface
{
    /**
     * @param Data $dataHelper
     */
    public function __construct(
        private readonly Data $dataHelper
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
}
