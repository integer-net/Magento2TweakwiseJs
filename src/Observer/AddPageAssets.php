<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Config as PageConfig;
use Tweakwise\TweakwiseJs\Model\Config;

class AddPageAssets implements ObserverInterface
{
    /**
     * @param PageConfig $pageConfig
     * @param Config $config
     */
    public function __construct(
        private readonly PageConfig $pageConfig,
        private readonly Config $config
    ) {
    }

    /**
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        // TODO: CAN WE SPECIFY ON WHICH PAGE TYPES THE SCRIPTS MUST BE LOADED?
        if (!$this->config->isEnabled()) {
            return;
        }

        $instanceKey = $this->config->getInstanceKey();

        $this->pageConfig->addRemotePageAsset(
            'https://gateway.tweakwisenavigator.net/js/starter.js',
            'link',
            ['attributes' => ['rel' => 'preload', 'as' => 'script']]
        );
        $this->pageConfig->addRemotePageAsset(
            sprintf('https://gateway.tweakwisenavigator.net/js/%s/tweakwise.js', $instanceKey),
            'link',
            ['attributes' => ['rel' => 'preload', 'as' => 'script']]
        );
        $this->pageConfig->addRemotePageAsset(
            sprintf('https://gateway.tweakwisenavigator.net/js/%s/tweakwise.js', $instanceKey),
            'js',
            ['attributes' => [
                'data-failover' => sprintf('https://gateway.tweakwisenavigator.com/js/%s/tweakwise.js', $instanceKey),
                'onerror' => 'window.tweakwiseFailover(this.dataset.failover)'
            ]]
        );
    }
}
