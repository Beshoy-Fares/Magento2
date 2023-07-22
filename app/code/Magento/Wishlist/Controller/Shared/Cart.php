<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Controller\Shared;

use Exception;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\Item\OptionFactory;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item\Option\Collection as OptionCollection;

/**
 * Wishlist Cart Controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Cart extends Action implements HttpPostActionInterface
{
    /**
     * @param ActionContext $context
     * @param CustomerCart $cart
     * @param OptionFactory $optionFactory
     * @param ItemFactory $itemFactory
     * @param CartHelper $cartHelper
     * @param Escaper $escaper
     */
    public function __construct(
        ActionContext $context,
        protected readonly CustomerCart $cart,
        protected readonly OptionFactory $optionFactory,
        protected readonly ItemFactory $itemFactory,
        protected readonly CartHelper $cartHelper,
        protected readonly Escaper $escaper
    ) {
        parent::__construct($context);
    }

    /**
     * Add shared wishlist item to shopping cart
     *
     * If Product has required options - redirect
     * to product view page with message about needed defined required options
     *
     * @return Redirect
     */
    public function execute()
    {
        $itemId = (int)$this->getRequest()->getParam('item');

        /* @var $item Item */
        $item = $this->itemFactory->create()
            ->load($itemId);

        $redirectUrl = $this->_redirect->getRefererUrl();

        try {
            /** @var OptionCollection $options */
            $options = $this->optionFactory->create()
                ->getCollection()->addItemFilter([$itemId]);
            $item->setOptions($options->getOptionsByItem($itemId));
            $item->addToCart($this->cart);

            $this->cart->save();

            if (!$this->cart->getQuote()->getHasError()) {
                $message = __(
                    'You added %1 to your shopping cart.',
                    $this->escaper->escapeHtml($item->getProduct()->getName())
                );
                $this->messageManager->addSuccessMessage($message);
            }

            if ($this->cartHelper->getShouldRedirectToCart()) {
                $redirectUrl = $this->cartHelper->getCartUrl();
            }
        } catch (ProductException $e) {
            $this->messageManager->addErrorMessage(__('This product(s) is out of stock.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addNoticeMessage($e->getMessage());
            $redirectUrl = $item->getProductUrl();
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t add the item to the cart right now.'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($redirectUrl);

        return $resultRedirect;
    }
}
