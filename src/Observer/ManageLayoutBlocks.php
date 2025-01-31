<?php

declare(strict_types=1);

namespace Tweakwise\TweakwiseJs\Observer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Layout;
use Tweakwise\TweakwiseJs\Model\Config;
use Tweakwise\TweakwiseJs\Model\Enum\SearchType;

class ManageLayoutBlocks implements ObserverInterface
{
    /**
     * @var Layout
     */
    private Layout $layout;

    /**
     * @var bool|null
     */
    private ?bool $isHyva = null;

    /**
     * @param Http $request
     * @param Config $config
     * @param Resolver $layerResolver
     * @param DesignInterface $viewDesign
     */
    public function __construct(
        private readonly Http $request,
        private readonly Config $config,
        private readonly Resolver $layerResolver,
        private readonly DesignInterface $viewDesign
    ) {
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->layout = $observer->getLayout();

        $this->addDefaultHandle();
        $this->addSearchHandle();

        if ($this->shouldAddAddToCartWishlistHandle()) {
            $this->addAddToCartWishlistHandle();
        }

        if (!$this->isCategoryPage() || !$this->shouldShowTweakwiseJsCategoryViewBlock()) {
            return;
        }

        $this->addMerchandisingHandle();
    }

    /**
     * @return void
     */
    private function addDefaultHandle(): void
    {
        $this->layout->getUpdate()->addHandle('tweakwisejs_default');
    }

    /**
     * @return void
     */
    private function addSearchHandle(): void
    {
        $this->layout->getUpdate()->addHandle('tweakwisejs_search');
    }

    /**
     * @return void
     */
    private function addMerchandisingHandle(): void
    {
        $this->layout->getUpdate()->addHandle($this->getHandleName('tweakwisejs_merchandising'));
    }

    /**
     * @return void
     */
    private function addAddToCartWishlistHandle(): void
    {
        $this->layout->getUpdate()->addHandle($this->getHandleName('tweakwisejs_add_to'));
    }

    /**
     * @return bool
     */
    private function isCategoryPage(): bool
    {
        return $this->request->getFullActionName() === 'catalog_category_view';
    }

    /**
     * @return bool
     */
    private function isSearchResultsPage(): bool
    {
        return $this->request->getFullActionName() === 'catalogsearch_results_index';
    }

    /**
     * @return bool
     */
    private function shouldShowTweakwiseJsCategoryViewBlock(): bool
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
     * @return bool
     */
    private function shouldAddAddToCartWishlistHandle(): bool
    {
        return $this->isCategoryPage() ||
            $this->isSearchResultsPage() ||
            $this->config->getSearchType()->value === SearchType::INSTANT_SEARCH->value;
    }

    /**
     * @return bool
     */
    private function isHyva(): bool
    {
        if ($this->isHyva !== null) {
            return $this->isHyva;
        }

        $theme = $this->viewDesign->getDesignTheme();
        while ($theme) {
            if (str_starts_with($theme->getCode(), 'Hyva/')) {
                $this->isHyva = true;
                return $this->isHyva;
            }
            $theme = $theme->getParentTheme();
        }

        $this->isHyva = false;
        return $this->isHyva;
    }

    /**
     * @param string $handle
     * @return string
     */
    private function getHandleName(string $handle): string
    {
        return $this->isHyva() ? sprintf('hyva_%s', $handle) : $handle;
    }
}
