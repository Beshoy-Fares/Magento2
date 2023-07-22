<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Wishlist\Model;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Wishlist ItemCarrier Controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class ItemCarrier
{
    /**
     * @param Session $customerSession
     * @param LocaleQuantityProcessor $quantityProcessor
     * @param Cart $cart
     * @param Logger $logger
     * @param WishlistHelper $helper
     * @param CartHelper $cartHelper
     * @param UrlInterface $urlBuilder
     * @param MessageManager $messageManager
     * @param RedirectInterface $redirector
     */
    public function __construct(
        protected readonly Session $customerSession,
        protected readonly LocaleQuantityProcessor $quantityProcessor,
        protected readonly Cart $cart,
        protected readonly Logger $logger,
        protected readonly WishlistHelper $helper,
        protected readonly CartHelper $cartHelper,
        protected readonly UrlInterface $urlBuilder,
        protected readonly MessageManager $messageManager,
        protected readonly RedirectInterface $redirector
    ) {
    }

    /**
     * Move all wishlist item to cart
     *
     * @param Wishlist $wishlist
     * @param array $qtys
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function moveAllToCart(Wishlist $wishlist, $qtys)
    {
        $isOwner = $wishlist->isOwner($this->customerSession->getCustomerId());

        $messages = [];
        $addedProducts = [];
        $notSalable = [];

        $cart = $this->cart;
        $collection = $wishlist->getItemCollection()->setVisibilityFilter();

        foreach ($collection as $item) {
            /** @var $item Item */
            try {
                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->quantityProcessor->process($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                $item->getProduct()->setDisableAddToCart($disableAddToCart);
                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedProducts[] = $item->getProduct();
                }
            } catch (LocalizedException $e) {
                if ($e instanceof ProductException) {
                    $notSalable[] = $item;
                } else {
                    $messages[] = __('%1 for "%2".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }

                $cartItem = $cart->getQuote()->getItemByProduct($item->getProduct());
                if ($cartItem) {
                    $cart->getQuote()->deleteItem($cartItem);
                }
            } catch (Exception $e) {
                $this->logger->critical($e);
                $messages[] = __('We can\'t add this item to your shopping cart right now.');
            }
        }

        if ($isOwner) {
            $indexUrl = $this->helper->getListUrl($wishlist->getId());
        } else {
            $indexUrl = $this->urlBuilder->getUrl('wishlist/shared', ['code' => $wishlist->getSharingCode()]);
        }
        if ($this->cartHelper->getShouldRedirectToCart()) {
            $redirectUrl = $this->cartHelper->getCartUrl();
        } elseif ($this->redirector->getRefererUrl()) {
            $redirectUrl = $this->redirector->getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if ($notSalable) {
            $products = [];
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __(
                'We couldn\'t add the following product(s) to the shopping cart: %1.',
                join(', ', $products)
            );
        }

        if ($messages) {
            foreach ($messages as $message) {
                $this->messageManager->addErrorMessage($message);
            }
            $redirectUrl = $indexUrl;
        }

        if ($addedProducts) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t update the Wish List right now.'));
                $redirectUrl = $indexUrl;
            }

            $products = [];
            foreach ($addedProducts as $product) {
                /** @var $product Product */
                $products[] = '"' . $product->getName() . '"';
            }

            $this->messageManager->addSuccessMessage(
                __('%1 product(s) have been added to shopping cart: %2.', count($addedProducts), join(', ', $products))
            );

            // save cart and collect totals
            $cart->save()->getQuote()->collectTotals();
        }
        $this->helper->calculate();
        return $redirectUrl;
    }
}
