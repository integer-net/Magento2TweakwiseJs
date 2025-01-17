<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Controller\Results;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Index implements HttpGetActionInterface
{
    /**
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        private readonly ResultFactory $resultFactory
    ) {
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
