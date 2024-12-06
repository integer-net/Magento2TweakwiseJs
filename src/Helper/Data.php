<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        private readonly StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
    }

    /**
     * @param int $entityId
     * @return string
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getTweakwiseId(int $entityId): string
    {
        $storeId = $this->storeManager->getStore()->getId();
        return '100014';
//        return $this->exportHelper->getTweakwiseId($storeId, $entityId); // TODO: require export module
    }
}
