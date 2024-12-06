<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Observer;

use Magento\Catalog\Block\Category\View;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Tweakwise\TweakwiseJs\Model\Config;
use Tweakwise\TweakwiseJs\ViewModel\TweakwiseJs;

class ManageLayoutBlocks implements ObserverInterface
{
    /**
     * @param Http $request
     * @param Config $config
     * @param TweakwiseJs $viewModel
     * @param Resolver $layerResolver
     */
    public function __construct(
        private readonly Http $request,
        private readonly Config $config,
        private readonly TweakwiseJs $viewModel,
        private readonly Resolver $layerResolver
    ) {
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $layout = $observer->getLayout();

        $this->addDefaultBlock($layout);

        if (!$this->isCategoryPage() || !$this->showTweakwiseJsCategoryViewBlock()) {
            return;
        }

        $this->addTweakwiseJsCategoryViewBlock($layout);
    }

    /**
     * @param Layout $layout
     * @return void
     */
    private function addDefaultBlock(Layout $layout): void
    {
        $blockName = 'tweakwise-js-default';
        $layout->createBlock(Template::class, $blockName)
            ->setTemplate('Tweakwise_TweakwiseJs::default.phtml');
        $layout->setChild('after.body.start', $blockName, $blockName);
    }

    /**
     * @return bool
     */
    private function isCategoryPage(): bool
    {
        return $this->request->getFullActionName() === 'catalog_category_view';
    }

    /**
     * @param Layout $layout
     * @return void
     */
    private function addTweakwiseJsCategoryViewBlock(Layout $layout): void
    {
        $blockName = 'tweakwise-js-lister';
        $layout->createBlock(
            View::class,
            $blockName,
            [
                'data' => [
                    'view_model' => $this->viewModel
                ]
            ]
        )->setTemplate('Tweakwise_TweakwiseJs::category/listing.phtml');
        $layout->setChild('page.wrapper', $blockName, $blockName);
    }

    /**
     * @return bool
     */
    private function showTweakwiseJsCategoryViewBlock(): bool
    {
        if (!$this->config->isMerchandisingEnabled()) {
            return false;
        }

        $currentCategory = $this->layerResolver->get()->getCurrentCategory();
        if (!$currentCategory) {
            return false;
        }

        $displayMode = $currentCategory->getDisplayMode();
        if ($displayMode && $displayMode === Category::DM_PAGE) {
            return false;
        }

        return true;
    }
}
