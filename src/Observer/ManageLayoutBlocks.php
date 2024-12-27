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
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\Layout;
use Tweakwise\TweakwiseJs\Model\Config;
use Tweakwise\TweakwiseJs\ViewModel\Merchandising;
use Tweakwise\TweakwiseJs\ViewModel\Search;

class ManageLayoutBlocks implements ObserverInterface
{
    /**
     * @var Layout
     */
    private Layout $layout;

    /**
     * @param Http $request
     * @param Config $config
     * @param Merchandising $merchandisingViewModel
     * @param Search $searchViewModel
     * @param Resolver $layerResolver
     */
    public function __construct(
        private readonly Http $request,
        private readonly Config $config,
        private readonly Merchandising $merchandisingViewModel,
        private readonly Search $searchViewModel,
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

        $this->layout = $observer->getLayout();

        $this->addDefaultBlock();
        $this->addSearchBlock();

        if (!$this->isCategoryPage() || !$this->showTweakwiseJsCategoryViewBlock()) {
            return;
        }

        $this->manageCategoryViewLayoutElements();
    }

    /**
     * @return void
     */
    private function addDefaultBlock(): void
    {
        $blockName = 'tweakwise-js-default';
        $this->layout->createBlock(Template::class, $blockName)
            ->setTemplate('Tweakwise_TweakwiseJs::js/default.phtml');
        $this->layout->setChild('after.body.start', $blockName, $blockName);
    }

    /**
     * @return bool
     */
    private function isCategoryPage(): bool
    {
        return $this->request->getFullActionName() === 'catalog_category_view';
    }

    /**
     * @return void
     */
    private function manageCategoryViewLayoutElements(): void
    {
        $this->addTweakwiseJsCategoryViewBlock();
        $this->addTweakwiseJsAddToCartBlock();
        $this->removeMagentoCategoryViewLayoutElements();
    }

    /**
     * @return void
     */
    private function addTweakwiseJsCategoryViewBlock(): void
    {
        $blockName = 'tweakwise-js-lister';
        $this->layout->createBlock(
            View::class,
            $blockName,
            [
                'data' => [
                    'view_model' => $this->merchandisingViewModel
                ]
            ]
        )->setTemplate('Tweakwise_TweakwiseJs::js/category/listing.phtml');
        $this->layout->setChild('page.wrapper', $blockName, $blockName);
    }

    /**
     * @return void
     */
    private function addTweakwiseJsAddToCartBlock(): void
    {
        $blockName = 'tweakwise-js-add-to-cart-js';
        $this->layout->createBlock(
            View::class,
            $blockName,
            [
                'data' => [
                    'view_model' => $this->merchandisingViewModel
                ]
            ]
        )->setTemplate('Tweakwise_TweakwiseJs::js/category/add-to-cart-js.phtml');
        $this->layout->setChild('page.wrapper', $blockName, $blockName);
    }

    /**
     * @return void
     */
    private function removeMagentoCategoryViewLayoutElements(): void
    {
        $this->addEmptyBlock();
        $this->layout->unsetElement('main');
        $this->layout->unsetElement('div.sidebar.main');
        $this->layout->unsetElement('div.sidebar.additional');
    }

    /**
     * Function to add an empty block to the "columns" container, because Magento doesn't render empty containers
     * @return void
     */
    private function addEmptyBlock(): void
    {
        $blockName = 'empty-block';
        $this->layout->createBlock(Text::class, $blockName, ['data' => ['text' => ' ']]);
        $this->layout->setChild('columns', $blockName, $blockName);
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

    /**
     * @return void
     */
    private function addSearchBlock(): void
    {
        $blockName = 'tweakwise-js-search';
        $this->layout->createBlock(
            Template::class,
            $blockName,
            [
                'data' => [
                    'view_model' => $this->searchViewModel
                ]
            ]
        )->setTemplate('Tweakwise_TweakwiseJs::js/search.phtml');
        $this->layout->setChild('after.body.start', $blockName, $blockName);
    }
}
